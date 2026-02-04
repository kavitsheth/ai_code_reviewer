# 🚀 AI Code Reviewer - Qwen2.5 Local LLM

[![GitHub stars](https://img.shields.io/github/stars/kavitsheth/ai_code_reviewer?style=social)](https://github.com/kavitsheth/ai_code_reviewer)
[![License](https://img.shields.io/github/license/kavitsheth/ai_code_reviewer))](LICENSE)

**Instant AI-powered code fixes** in **5 languages** - **100% LOCAL** (No API keys, No cloud!)

## 🎯 **Demo**
❌ Issues:

Syntax errors: #import <stdio.h> should be #include <iostream>. cout::Hello should be cout << "Hello";.
📊 Complexity Analysis: Current: Time O(1), Space O(1) Improved: Time O(1), Space O(1)

✅ Recommended Fix:

#include <iostream>
using namespace std;

int main() {
    cout << "Hello";
    return 0;
}



## ✨ **Features**
- ✅ **5 Languages**: PHP,JavaScript, Python, C++, Java, TypeScript
- ✅ **Local AI**: Qwen2.5-Coder-3B (~2GB GGUF model)
- ✅ **Real-time syntax highlighting**
- ✅ **Production-ready fixes** (complete imports/headers)
- ✅ **No external APIs** - Works offline!

## 🏗️ **Tech Stack**
Frontend: Html , CSS , Jquery , Blade
Backend: Express.js (Port 3000) → FastAPI (Port 8000)
AI: Qwen2.5-Coder-7B-Instruct-Q6_K + llama-cpp-python
Model: Qwen2.5-Coder-7B-Instruct-Q6_K.gguf (~ 6.26 GB)


## 🚀 **Quick Start (3 Terminals)**

### **Prerequisites**
```bash
# System requirements
PHP 8.5 | Laravel 12+ | Python 3.10+ | 10GB+ RAM (for Qwen2.5 model)
1. Clone & Setup
git clone https://github.com/kavitsheth/ai-code-reviewer.git

2. AI Backend (FastAPI + Qwen2.5) - Terminal 1
cd ai-local-service
pip install -r requirements.txt

# Download model (~2GB, one-time)
mkdir -p models
# Download: Qwen2.5-Coder-7B-Instruct-Q6_K.gguf
# HF Link: https://huggingface.co/bartowski/Qwen2.5-Coder-7B-Instruct-GGUF/blob/main/Qwen2.5-Coder-7B-Instruct-Q6_K.gguf

# Start FastAPI
python local_llm_api.py
if not works tey this : uvicorn local_llm_api:app --host 0.0.0.0 --port 8000 --reload
# http://localhost:8000/docs (API docs)
3. Laravel - Terminal 2
cd ..
php artisan serve --port 8002
# http://127.0.0.1:8002/
5. Open Browser

http://localhost:8002 ← Your AI Code Reviewer!
🧪 Usage Examples
Language	Input Code	AI Output
C++	print("Hello");	#include <iostream> std::cout << "Hello" << std::endl;
Python	print "Hello"	print("Hello")
JavaScript	print("Hello");	console.log("Hello");
Java	print("Hello");	System.out.println("Hello");

🤝 Contributing
Fork repository

git checkout -b feature/amazing-feature

Commit changes: git commit -m "Add amazing feature"

Push: git push origin feature/amazing-feature

Open Pull Request

📄 License
MIT License - Use freely!

👨‍💻 Author
Kavit Sheth - Full Stack AI Developer
LinkedIn | Portfolio

⭐ Star this repo if you found it useful! 🚀
