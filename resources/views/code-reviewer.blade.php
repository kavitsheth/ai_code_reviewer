<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kavit's Code Reviewer</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Marked.js -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- DOMPurify (IMPORTANT for AI output safety) -->
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.6/dist/purify.min.js"></script>

    <!-- Prism.js -->
    <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-python.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-java.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-c.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-php.min.js"></script>

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body {
            height:100%;
            background:#0d1117;
            color:#f0f6fc;
            font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .header {
            padding:1.5rem 2rem;
            background:#161b22;
            border-bottom:1px solid #30363d;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .header h1 {
            font-size:1.75rem;
            color:#58a6ff;
        }

        .status {
            padding:0.5rem 1.2rem;
            background:#21262d;
            border-radius:6px;
            font-size:0.85rem;
            color:#8b949e;
        }

        .status.loading {
            background:linear-gradient(90deg,#9a7ec0,#d2b8ff);
            color:#0d1117;
            font-weight:600;
            animation:pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%,100% { opacity:1 }
            50% { opacity:0.8 }
        }

        main {
            height:calc(100vh - 80px);
            display:flex;
            gap:1rem;
            padding:1.5rem;
        }

        .left, .right {
            flex:1;
            border:1px solid #30363d;
            border-radius:8px;
            display:flex;
            flex-direction:column;
            overflow:hidden;
        }

        .language-selector {
            padding:1rem;
            background:#161b22;
            border-bottom:1px solid #30363d;
        }

        select {
            width:100%;
            background:#21262d;
            border:1px solid #30363d;
            color:#f0f6fc;
            padding:0.7rem;
            border-radius:6px;
        }

        textarea {
            flex:1;
            background:#0d1117;
            color:#f0f6fc;
            border:none;
            padding:1rem;
            font-family:"Fira Code", monospace;
            font-size:15px;
            resize:none;
            outline:none;
        }

        .button-container {
            padding:1rem;
            background:#161b22;
            border-top:1px solid #30363d;
        }

        button {
            width:100%;
            padding:0.9rem;
            background:#238636;
            color:white;
            border:none;
            border-radius:6px;
            cursor:pointer;
            font-size:1rem;
        }

        button:disabled {
            background:#384d54;
            cursor:not-allowed;
        }

        .right {
            padding:1.5rem;
            overflow-y:auto;
        }

        pre {
            background:#161b22;
            border:1px solid #30363d;
            padding:1rem;
            border-radius:6px;
            margin-top:1rem;
            overflow-x:auto;
            white-space:pre-wrap;
            word-wrap:break-word;
        }

        code {
            font-family:"Fira Code", monospace;
            line-height:1.6;
        }

        strong { color:#3fb950; }
        ul { margin-left:1.2rem; }

        @media(max-width:768px) {
            main { flex-direction:column; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Kavit's Code Reviewer</h1>
    <div id="status" class="status">Ready</div>
</div>

<main>
    <!-- LEFT -->
    <div class="left">
        <div class="language-selector">
            <select id="language">
                <option value="javascript">JavaScript</option>
                <option value="python">Python</option>
                <option value="java">Java</option>
                <option value="cpp">C++</option>
                <option value="typescript">TypeScript</option>
                <option value="php">PHP</option>
            </select>
        </div>

        <textarea id="code">function sum() { return 1 + 1 }</textarea>

        <div class="button-container">
            <button id="reviewBtn">Review Code</button>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div id="review">
            Write your code → Click Review → Get instant AI feedback
        </div>
    </div>
</main>

<script>
marked.setOptions({
    highlight: function(code, lang) {
        if (Prism.languages[lang]) {
            return Prism.highlight(code, Prism.languages[lang], lang);
        }
        return code;
    }
});

$(function () {

    function requestAiReview(code, language, callback, errorCallback) {
        $.ajax({
            url: '/ai/get-review',
            method: 'POST',
            data: {
                code,
                language,
                _token: '{{ csrf_token() }}'
            },
            timeout: 180000,
            success(res) {
                let content = '';

                if (typeof res === 'string') {
                    content = res;
                } else if (res?.review?.review) {
                    content = res.review.review;
                } else if (res?.review) {
                    content = res.review;
                } else {
                    content = '⚠️ Unexpected response:\n\n```json\n' +
                        JSON.stringify(res, null, 2) +
                        '\n```';
                }

                callback(content);
            },
            error(xhr, status) {
                const msg = status === 'timeout'
                    ? '❌ AI request timed out'
                    : '❌ Backend error';
                errorCallback(msg);
            }
        });
    }

    function startTimer(elem) {
        const start = Date.now();
        return setInterval(() => {
            elem.text('Loading... ⏱');
        }, 100);
    }

    $('#reviewBtn').on('click', function () {
        const code = $('#code').val();
        const language = $('#language').val();
        const status = $('#status');
        const review = $('#review');
        const btn = $(this);

        review.html('');
        btn.prop('disabled', true);
        status.addClass('loading');

        const timer = startTimer(status);

        requestAiReview(code, language,
            (content) => {
                clearInterval(timer);
                const cleanHtml = DOMPurify.sanitize(marked.parse(content));
                review.html(cleanHtml);
                status.text('Done ✅').removeClass('loading');
                btn.prop('disabled', false);
            },
            (err) => {
                clearInterval(timer);
                review.html('<strong>' + err + '</strong>');
                status.text('Error').removeClass('loading');
                btn.prop('disabled', false);
            }
        );
    });

});
</script>

</body>
</html>
