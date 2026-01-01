<?php
/**
 * Verdant AI Core - Unified AI Handler
 * Grok API primary + Ollama local fallback
 * NERDC curriculum-aligned for Nigerian education
 */

class VerdantAI
{
    private static $instance = null;
    private $grokApiKey;
    private $ollamaUrl;
    private $model;
    private $useOffline;

    // NERDC curriculum context for prompts
    private const NERDC_CONTEXT = "You are Verdant AI, an educational assistant for Nigerian schools following the NERDC (Nigerian Educational Research and Development Council) curriculum. You help students learn, teachers plan lessons, and parents understand their children's progress. Always align responses with Nigerian Basic Education Curriculum (BEC) and Senior Secondary Education Curriculum (SSEC). Be encouraging, clear, and culturally appropriate for Nigerian students.";

    private function __construct()
    {
        $this->grokApiKey = getenv('GROK_API_KEY') ?: '';
        $this->ollamaUrl = getenv('OLLAMA_URL') ?: 'http://localhost:11434';
        $this->model = getenv('AI_MODEL') ?: 'llama3.2:3b';
        $this->useOffline = empty($this->grokApiKey);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Generate AI response with NERDC context
     */
    public function generate(string $prompt, array $options = []): array
    {
        $role = $options['role'] ?? 'student';
        $class = $options['class'] ?? '';
        $subject = $options['subject'] ?? '';

        // Build context-aware prompt
        $contextPrompt = self::NERDC_CONTEXT . "\n\n";

        if ($role === 'student' && $class) {
            $contextPrompt .= "Student is in {$class}. ";
        }
        if ($subject) {
            $contextPrompt .= "Subject focus: {$subject}. ";
        }

        $contextPrompt .= "\n\nUser ({$role}): {$prompt}\n\nVerdant AI:";

        // Try Grok first, fallback to Ollama
        if (!$this->useOffline) {
            $response = $this->callGrok($contextPrompt);
            if ($response['success']) {
                return $response;
            }
        }

        // Fallback to Ollama
        return $this->callOllama($contextPrompt);
    }

    /**
     * Call Grok API (cloud)
     */
    private function callGrok(string $prompt): array
    {
        $url = 'https://api.x.ai/v1/chat/completions';

        $data = [
            'model' => 'grok-beta',
            'messages' => [
                ['role' => 'system', 'content' => self::NERDC_CONTEXT],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->grokApiKey
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'response' => $result['choices'][0]['message']['content'] ?? 'No response',
                'source' => 'grok'
            ];
        }

        return ['success' => false, 'error' => 'Grok API failed'];
    }

    /**
     * Call Ollama API (local/offline)
     */
    private function callOllama(string $prompt): array
    {
        $url = $this->ollamaUrl . '/api/generate';

        $data = [
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => 0.7,
                'num_predict' => 500
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 60
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'response' => $result['response'] ?? 'No response',
                'source' => 'ollama'
            ];
        }

        // If Ollama also fails, return helpful message
        return [
            'success' => true,
            'response' => "I'm currently offline. Please check your internet connection or ensure Ollama is running locally. For now, I can still help with basic questions about the NERDC curriculum!",
            'source' => 'fallback'
        ];
    }

    /**
     * Generate lesson plan (NERDC-aligned)
     */
    public function generateLessonPlan(string $class, string $subject, string $topic, int $duration = 40): array
    {
        $prompt = "Create a detailed {$duration}-minute lesson plan for {$class} {$subject} on the topic: {$topic}.

Include:
1. Learning Objectives (3-4, measurable)
2. Materials Needed
3. Introduction (5 min)
4. Main Teaching Activity (20 min)
5. Student Practice (10 min)
6. Assessment Questions (3-5)
7. Homework Assignment

Align with NERDC Nigerian curriculum standards. Use simple English suitable for Nigerian students.";

        return $this->generate($prompt, ['role' => 'teacher', 'class' => $class, 'subject' => $subject]);
    }

    /**
     * Solve homework/explain concept
     */
    public function solveHomework(string $question, string $subject, string $class = ''): array
    {
        $prompt = "A Nigerian student asks: {$question}

Subject: {$subject}
" . ($class ? "Class: {$class}" : "") . "

Please:
1. Solve/explain step-by-step
2. Use simple language
3. Give 1-2 practice problems
4. Encourage the student";

        return $this->generate($prompt, ['role' => 'student', 'class' => $class, 'subject' => $subject]);
    }

    /**
     * Generate quiz questions
     */
    public function generateQuiz(string $class, string $subject, string $topic, int $count = 5): array
    {
        $prompt = "Generate {$count} multiple-choice quiz questions for {$class} {$subject} on topic: {$topic}.

Format each question as:
Q1. [Question]
A) [Option]
B) [Option]
C) [Option]
D) [Option]
Answer: [Letter]

Align with NERDC curriculum.";

        return $this->generate($prompt, ['role' => 'teacher', 'class' => $class, 'subject' => $subject]);
    }

    /**
     * Get student progress summary (for parents)
     */
    public function getProgressSummary(array $studentData): array
    {
        $name = $studentData['name'] ?? 'Your child';
        $class = $studentData['class'] ?? '';
        $attendance = $studentData['attendance'] ?? 90;
        $avgGrade = $studentData['avg_grade'] ?? 75;
        $weakSubjects = $studentData['weak_subjects'] ?? [];

        $prompt = "Summarize this student's progress for their parent:

Student: {$name}
Class: {$class}
Attendance: {$attendance}%
Average Grade: {$avgGrade}%
Weak Areas: " . implode(', ', $weakSubjects) . "

Provide:
1. Brief summary (2-3 sentences)
2. Strengths
3. Areas to improve
4. Tips for parents to help at home

Be encouraging and culturally appropriate for Nigerian parents.";

        return $this->generate($prompt, ['role' => 'parent']);
    }
}

// Helper function for quick access
function verdant_ai(): VerdantAI
{
    return VerdantAI::getInstance();
}
