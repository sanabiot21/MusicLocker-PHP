<?php

namespace MusicLocker\Controllers;

/**
 * Base Controller
 * Provides common functionality for all controllers
 */
abstract class BaseController
{
    protected array $data = [];
    protected ?int $userId = null;
    
    public function __construct()
    {
        $this->startSession();
        $this->initializeAuth();
        $this->initializeData();
    }
    
    /**
     * Start PHP session
     */
    protected function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Initialize authentication state
     */
    protected function initializeAuth(): void
    {
        $this->userId = current_user_id();
    }
    
    /**
     * Initialize common view data
     */
    protected function initializeData(): void
    {
        $this->data = [
            'title' => config('app.name', 'Music Locker'),
            'user_id' => $this->userId,
            'is_logged_in' => is_logged_in(),
            'csrf_token' => csrf_token(),
            'base_url' => base_url(),
            'current_page' => $this->getCurrentPage(),
            'flash_messages' => $this->getFlashMessages()
        ];
    }
    
    /**
     * Get current page identifier
     */
    protected function getCurrentPage(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($path, PHP_URL_PATH);
        
        return match($path) {
            '/' => 'home',
            '/login' => 'login',
            '/register' => 'register',
            '/dashboard' => 'dashboard',
            '/music' => 'music',
            '/music/add' => 'music-add',
            '/profile' => 'profile',
            default => 'other'
        };
    }
    
    /**
     * Get and clear flash messages
     */
    protected function getFlashMessages(): array
    {
        $messages = [];
        $flashTypes = ['success', 'error', 'warning', 'info'];
        
        foreach ($flashTypes as $type) {
            if ($message = flash($type)) {
                $messages[$type] = $message;
            }
        }
        
        return $messages;
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (!is_logged_in()) {
            flash('error', 'Please log in to access this page.');
            redirect(route_url('login'));
        }
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCSRF(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return validate_csrf($_POST['_token'] ?? '');
        }
        
        return true;
    }
    
    /**
     * Set page title
     */
    protected function setTitle(string $title): void
    {
        $this->data['title'] = $title . ' - ' . config('app.name', 'Music Locker');
    }
    
    /**
     * Add data to view
     */
    protected function addData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }
    
    /**
     * Add multiple data to view
     */
    protected function addDataArray(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }
    
    /**
     * Render view template
     */
    protected function view(string $template, array $data = []): void
    {
        $viewData = array_merge($this->data, $data);
        
        // Extract variables for the template
        extract($viewData);
        
        // Build template path
        $templatePath = __DIR__ . '/../Views/' . str_replace('.', '/', $template) . '.php';
        
        if (!file_exists($templatePath)) {
            throw new \Exception("View template not found: $template");
        }
        
        // Start output buffering
        ob_start();
        
        // Include the template
        require $templatePath;
        
        // Get the content
        $content = ob_get_clean();
        
        // Check if this is a layout template
        if (strpos($template, 'layouts.') === 0) {
            echo $content;
            return;
        }
        
        // Check if we need to wrap in a layout
        if (!isset($no_layout) || !$no_layout) {
            $this->renderLayout($content, $viewData);
        } else {
            echo $content;
        }
    }
    
    /**
     * Render content within layout
     */
    protected function renderLayout(string $content, array $data): void
    {
        $layout = $data['layout'] ?? 'app';
        $layoutPath = __DIR__ . '/../Views/layouts/' . $layout . '.php';
        
        if (!file_exists($layoutPath)) {
            echo $content;
            return;
        }
        
        // Extract data for layout
        extract($data);
        
        // Make content available to layout
        $main_content = $content;
        
        require $layoutPath;
    }
    
    /**
     * Return JSON response
     */
    protected function json(array $data, int $status = 200): void
    {
        json_response($data, $status);
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect(string $url): void
    {
        redirect($url);
    }
    
    /**
     * Handle form validation errors
     */
    protected function handleValidationErrors(array $errors): void
    {
        if (!empty($errors)) {
            flash('error', 'Please correct the errors below.');
            flash('validation_errors', $errors);
            $this->redirectBack();
        }
    }
    
    /**
     * Redirect back to previous page
     */
    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? route_url('home');
        redirect($referer);
    }
    
    /**
     * Get input from request
     */
    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    /**
     * Get all input from request
     */
    protected function allInput(): array
    {
        return array_merge($_GET, $_POST);
    }
    
    /**
     * Check if request is AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get request method
     */
    protected function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
    
    /**
     * Check if request method matches
     */
    protected function isMethod(string $method): bool
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }
}