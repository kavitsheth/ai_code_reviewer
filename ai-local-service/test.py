from llama_cpp import Llama

MODEL_PATH = "models/Qwen2.5-Coder-3B-Instruct-abliterated-Q4_K_M.gguf"  # ✅ Added .gguf

llm = Llama(
    model_path=MODEL_PATH,
    n_ctx=4096,
    n_threads=4,
    n_gpu_layers=0,
    use_metal=False,
    verbose=True
)

def chat(prompt):
    # ✅ QWEN2.5 FORMAT (NOT Phi-3)
    full_prompt = f"<|im_start|>system\nYou are a helpful coding assistant.<|im_end|>\n<|im_start|>user\n{prompt}<|im_end|>\n<|im_start|>assistant\n"
    
    print(f"🧑 You: {prompt}")
    output = llm(full_prompt, max_tokens=150, echo=False, stop=["<|im_end|>"])
    print(f"🤖 AI: {output['choices'][0]['text'].strip()}\n")

print("🚀 Qwen2.5-Coder 3B ready! (quit to exit)")
while True:
    user_input = input("You: ").strip()
    if user_input.lower() in ['quit', 'q']: 
        break
    chat(user_input)
