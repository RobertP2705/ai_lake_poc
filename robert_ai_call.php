<?php

error_reporting(0);
ini_set('display_errors', 0);

ob_start();


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$api_key = 'Haha you thought';
$model = 'claude-3-haiku-20240307';
$api_url = 'https://api.anthropic.com/v1/messages';

class APIResponse {
    public $success;
    public $data;
    public $error;

    public function __construct($success, $data = null, $error = null) {
        $this->success = $success;
        $this->data = $data;
        $this->error = $error;
    }
}

function callAnthropicAPI($prompt, $api_key, $model, $api_url) {
    if (empty($prompt)) {
        throw new Exception('Prompt cannot be empty');
    }

    $data = array(
        'model' => $model,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => $prompt
            )
        ),
        'max_tokens' => 1024
    );

    $ch = curl_init($api_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'x-api-key: ' . $api_key,
        'anthropic-version: 2023-06-01'
    ));

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);

    if ($http_code !== 200) {
        throw new Exception($response);
    }

    $result = json_decode($response, true);
    

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['prompt'])) {
            throw new Exception('No prompt provided');
        }

        $result = callAnthropicAPI($input['prompt'], $api_key, $model, $api_url);
        

        echo json_encode(new APIResponse(
            true,
            array(
                'response' => $result['content'][0]['text'],
                'model' => $model,
                'timestamp' => date('Y-m-d H:i:s')
            )
        ));

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(new APIResponse(
            false,
            null,
            $e->getMessage()
        ));
    }
} else {
    http_response_code(405);
    echo json_encode(new APIResponse(
        false,
        null,
        'Method not allowed'
    ));
}
?>
