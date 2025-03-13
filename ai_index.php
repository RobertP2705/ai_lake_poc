<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lake Cable AI</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/9.1.6/marked.min.js"></script>
    <style>
        body {
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .input-group {
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            font-family: inherit;
            margin-bottom: 10px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .response {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f8f9fa;
        }

        .error {
            color: #dc3545;
            padding: 10px;
            border: 1px solid #dc3545;
            border-radius: 4px;
            margin-top: 10px;
            background-color: #fce6e6;
        }

        .status {
            margin-top: 10px;
            font-style: italic;
            color: #666;
        }


    </style>
</head>
<body>
    <div class="container">
        <h1>Lake Cable AI</h1>
        
        <div class="input-group">
            <textarea id="prompt" placeholder="Enter your prompt here..."></textarea>
            <button id="submit">Send Request</button>
        </div>

        <div id="status" class="status"></div>
        <div id="error" class="error" style="display: none;"></div>
        <div id="response" class="response" style="display: none;"></div>
    </div>

    <script>
        const apiEndpoint = 'robert_ai_call.php'; 
        
        const promptInput = document.getElementById('prompt');
        const submitButton = document.getElementById('submit');
        const statusDiv = document.getElementById('status');
        const errorDiv = document.getElementById('error');
        const responseDiv = document.getElementById('response');

        async function sendRequest() {
            errorDiv.style.display = 'none';
            responseDiv.style.display = 'none';
            statusDiv.textContent = 'Sending request...';
            submitButton.disabled = true;

            try {
                const response = await fetch(apiEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        prompt:"You are a bot for an electrical cable making company. The company is named Lake Cable. Always refer to yourself as a bot. " + promptInput.value
                    })
                });

                const data = await response.json();

                if (data.success) {
                    const formattedResponse = marked.parse(data.data.response);
                    
                    responseDiv.innerHTML = formattedResponse;
                    responseDiv.style.display = 'block';
                    statusDiv.textContent = `Response received at ${data.data.timestamp}`;
                } else {
                    throw new Error(data.error || 'Unknown error occurred');
                }
            } catch (error) {
                errorDiv.textContent = `Error: ${error.message}`;
                errorDiv.style.display = 'block';
                statusDiv.textContent = 'Request failed';
            } finally {
                submitButton.disabled = false;
            }
        }

        submitButton.addEventListener('click', sendRequest);
        promptInput.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'Enter') {
                sendRequest();
            }
        });
    </script>
</body>
</html>
