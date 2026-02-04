from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field
from llama_cpp import Llama
import uvicorn
import os
import threading

app = FastAPI(
    title="Qwen2.5-Coder Senior Reviewer 🚀",
    version="2.2"
)

# MODEL_PATH = "models/Qwen2.5-Coder-3B-Instruct-abliterated-Q4_K_M.gguf"
MODEL_PATH = "models/Qwen2.5-Coder-7B-Instruct-Q6_K.gguf"

if not os.path.exists(MODEL_PATH):
    raise FileNotFoundError(f"❌ Model missing: {MODEL_PATH}")

llm = Llama(
    model_path=MODEL_PATH,
    n_ctx=4096,
    n_threads=os.cpu_count(),
    n_gpu_layers=0,
    verbose=False,
    f16_kv=True
)

# 🔒 llama_cpp is NOT async-safe
llm_lock = threading.Lock()

# =========================
# Request Models
# =========================

class CodeReviewRequest(BaseModel):
    code: str = Field(..., min_length=1, max_length=8000)
    language: str = Field(..., min_length=1, max_length=32)

# =========================
# Review Endpoint
# =========================

@app.post("/ai/review")
async def review_code(req: CodeReviewRequest):
    print(f"🔍 {req.language.upper()} ({len(req.code)} chars)")

    review_prompt = f"""<|im_start|>system
You are a senior {req.language} engineer with 10+ years experience.

CRITERIA FOR "Issues" (only these):
- Syntax errors (code won't run)
- Logic errors (wrong results)
- Security vulnerabilities  
- Performance: O(n²)→O(n), O(nlogn)→O(n), etc.

STYLISTIC (NOT issues):
- Semicolons, spacing, naming
- Optional best practices

COMPLEXITY RULES:
- ALWAYS analyze Time & Space complexity
- Flag ONLY if not optimal (O(n²) when O(n) possible)
- Show current vs improved Big-O

FORMAT (MANDATORY - copy exactly):

**❌ Issues:**
- None found
- [Issue 1]
- [Issue 2]

**📊 Complexity Analysis:**
Current: Time O(n²), Space O(n)
Improved: Time O(n), Space O(1)

**✅ Recommended Fix:**
```{req.language}
[COMPLETE fixed code - fully executable]
```<|im_end|>
<|im_start|>user
{req.code}<|im_end|>
<|im_start|>assistant"""

    try:
        with llm_lock:
            result = llm(
                review_prompt,
                max_tokens=1024,
                temperature=0.1,
                stop=["<|im_end|>"]
            )

        return {
            "review": result["choices"][0]["text"].strip()
        }

    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"LLM inference failed: {str(e)}"
        )
# @app.post("/ai/review")
# async def review_code(req: CodeReviewRequest):
#     print(f"🔍 {req.language.upper()} ({len(req.code)} chars)")

#     review_prompt = f"""<|im_start|>system
# You are a senior software engineer with 10+ years of experience.
# You specialize in {req.language.upper()}.

# STRICT RULES FOR RESPONSE:
# - Follow the format EXACTLY
# - Do NOT add extra sections
# - Do NOT explain outside the format

# MANDATORY OUTPUT FORMAT:

# **❌ Issues:**
# • [specific syntax / logic errors]

# **✅ Recommended Fix:**
# ```{req.language}
# [COMPLETE working code - executable]
# ```<|im_end|>
# <|im_start|>user
# {req.code}<|im_end|>
# <|im_start|>assistant"""

#     try:
#         with llm_lock:
#             result = llm(
#                 review_prompt,
#                 max_tokens=1024,
#                 temperature=0.1,
#                 stop=["<|im_end|>"]
#             )

#         return {
#             "review": result["choices"][0]["text"].strip()
#         }

#     except Exception as e:
#         raise HTTPException(
#             status_code=500,
#             detail=f"LLM inference failed: {str(e)}"
#         )

# =========================
# Local run
# =========================

if __name__ == "__main__":
    uvicorn.run(
        "local_llm_api:app",
        host="0.0.0.0",
        port=8000,
        reload=False
    )