<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo API - Frontend Integration Guide</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --accent: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --bg-gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --bg-gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 80px 20px 60px;
            color: white;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 20px rgba(0,0,0,0.2);
            animation: fadeInUp 0.8s ease-out;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 40px;
            opacity: 0.95;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .cta-button {
            display: inline-block;
            padding: 16px 40px;
            background: white;
            color: var(--primary);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        /* Main Content */
        .content {
            background: white;
            border-radius: 30px 30px 0 0;
            padding: 60px 20px;
            margin-top: -30px;
            position: relative;
            z-index: 1;
        }

        .section {
            margin-bottom: 80px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            background: var(--bg-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Quick Start Steps */
        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .step-card {
            background: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%);
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--bg-gradient);
        }

        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }

        .step-number {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: var(--bg-gradient);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .step-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #1f2937;
        }

        .step-card p {
            color: #6b7280;
            line-height: 1.8;
        }

        /* Code Examples */
        .code-examples {
            display: grid;
            gap: 30px;
        }

        .code-block {
            background: #1e293b;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .code-header {
            background: #0f172a;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .code-title {
            color: #e2e8f0;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .code-dots {
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .dot.red { background: #ef4444; }
        .dot.yellow { background: #f59e0b; }
        .dot.green { background: #10b981; }

        .code-content {
            padding: 25px;
            overflow-x: auto;
        }

        .code-content pre {
            margin: 0;
            color: #e2e8f0;
            font-family: 'Fira Code', 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.8;
        }

        .code-content code {
            color: #e2e8f0;
        }

        .keyword { color: #c792ea; }
        .string { color: #c3e88d; }
        .function { color: #82aaff; }
        .comment { color: #546e7a; }
        .number { color: #f78c6c; }

        /* Features Grid */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .feature-card {
            background: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%);
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .feature-card h4 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .feature-card p {
            color: #6b7280;
            font-size: 0.95rem;
        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            border-left: 4px solid var(--primary);
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
        }

        .info-box h4 {
            color: var(--primary-dark);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .info-box p {
            color: #1e40af;
            line-height: 1.8;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 40px 20px;
            color: white;
            opacity: 0.9;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .steps {
                grid-template-columns: 1fr;
            }
        }

        .highlight {
            background: linear-gradient(120deg, #fef3c7 0%, #fde68a 100%);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero">
        <div class="container">
            <h1>🚀 Todo API</h1>
            <p>Your complete guide to integrating with the Todo API. Perfect for frontend developers learning RESTful APIs, authentication, and modern web development.</p>
            <a href="/api-docs" class="cta-button">📚 View API Documentation →</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <!-- Quick Start Section -->
            <section class="section">
                <h2 class="section-title">⚡ Quick Start Guide</h2>
                <p style="font-size: 1.1rem; color: #6b7280; margin-bottom: 20px;">
                    Follow these simple steps to get started with the Todo API. You'll be making API calls in no time!
                </p>
                
                <div class="steps">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3>Register or Login</h3>
                        <p>Create a new account or login with existing credentials to get your authentication token.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3>Get Your Token</h3>
                        <p>After login, you'll receive a bearer token. Save this token - you'll need it for all API requests!</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3>Make API Calls</h3>
                        <p>Include your token in the <span class="highlight">Authorization</span> header and start making requests to the API endpoints.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3>Handle Responses</h3>
                        <p>All responses follow a consistent format. Check the <span class="highlight">message</span> and <span class="highlight">data</span> fields.</p>
                    </div>
                </div>
            </section>

            <!-- Code Examples Section -->
            <section class="section">
                <h2 class="section-title">💻 Code Examples</h2>
                <p style="font-size: 1.1rem; color: #6b7280; margin-bottom: 30px;">
                    Here are practical examples using JavaScript's Fetch API. Copy and adapt these to your project!
                </p>

                <div class="code-examples">
                    <!-- Register Example -->
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Register a New User</span>
                            <div class="code-dots">
                                <span class="dot red"></span>
                                <span class="dot yellow"></span>
                                <span class="dot green"></span>
                            </div>
                        </div>
                        <div class="code-content">
                            <pre><code><span class="keyword">const</span> <span class="function">registerUser</span> = <span class="keyword">async</span> () => {
  <span class="keyword">const</span> response = <span class="keyword">await</span> <span class="function">fetch</span>(<span class="string">'http://localhost:8000/api/register'</span>, {
    <span class="keyword">method</span>: <span class="string">'POST'</span>,
    <span class="keyword">headers</span>: {
      <span class="string">'Content-Type'</span>: <span class="string">'application/json'</span>
    },
    <span class="keyword">body</span>: <span class="function">JSON</span>.<span class="function">stringify</span>({
      <span class="keyword">name</span>: <span class="string">'John Doe'</span>,
      <span class="keyword">email</span>: <span class="string">'john@example.com'</span>,
      <span class="keyword">password</span>: <span class="string">'password123'</span>,
      <span class="keyword">password_confirmation</span>: <span class="string">'password123'</span>
    })
  });

  <span class="keyword">const</span> data = <span class="keyword">await</span> response.<span class="function">json</span>();
  <span class="keyword">return</span> data.<span class="keyword">data</span>.<span class="keyword">token</span>; <span class="comment">// Save this token!</span>
};</code></pre>
                        </div>
                    </div>

                    <!-- Login Example -->
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Login and Get Token</span>
                            <div class="code-dots">
                                <span class="dot red"></span>
                                <span class="dot yellow"></span>
                                <span class="dot green"></span>
                            </div>
                        </div>
                        <div class="code-content">
                            <pre><code><span class="keyword">const</span> <span class="function">login</span> = <span class="keyword">async</span> () => {
  <span class="keyword">const</span> response = <span class="keyword">await</span> <span class="function">fetch</span>(<span class="string">'http://localhost:8000/api/login'</span>, {
    <span class="keyword">method</span>: <span class="string">'POST'</span>,
    <span class="keyword">headers</span>: {
      <span class="string">'Content-Type'</span>: <span class="string">'application/json'</span>
    },
    <span class="keyword">body</span>: <span class="function">JSON</span>.<span class="function">stringify</span>({
      <span class="keyword">email</span>: <span class="string">'john@example.com'</span>,
      <span class="keyword">password</span>: <span class="string">'password123'</span>
    })
  });

  <span class="keyword">const</span> data = <span class="keyword">await</span> response.<span class="function">json</span>();
  <span class="keyword">const</span> token = data.<span class="keyword">data</span>.<span class="keyword">token</span>;
  
  <span class="comment">// Store token in localStorage or state</span>
  <span class="function">localStorage</span>.<span class="function">setItem</span>(<span class="string">'auth_token'</span>, token);
  <span class="keyword">return</span> token;
};</code></pre>
                        </div>
                    </div>

                    <!-- Get Todos Example -->
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Get All Todos (Authenticated Request)</span>
                            <div class="code-dots">
                                <span class="dot red"></span>
                                <span class="dot yellow"></span>
                                <span class="dot green"></span>
                            </div>
                        </div>
                        <div class="code-content">
                            <pre><code><span class="keyword">const</span> <span class="function">getTodos</span> = <span class="keyword">async</span> () => {
  <span class="keyword">const</span> token = <span class="function">localStorage</span>.<span class="function">getItem</span>(<span class="string">'auth_token'</span>);
  
  <span class="keyword">const</span> response = <span class="keyword">await</span> <span class="function">fetch</span>(<span class="string">'http://localhost:8000/api/todos'</span>, {
    <span class="keyword">method</span>: <span class="string">'GET'</span>,
    <span class="keyword">headers</span>: {
      <span class="string">'Authorization'</span>: <span class="string">`Bearer ${token}`</span>,
      <span class="string">'Content-Type'</span>: <span class="string">'application/json'</span>
    }
  });

  <span class="keyword">const</span> data = <span class="keyword">await</span> response.<span class="function">json</span>();
  <span class="keyword">return</span> data.<span class="keyword">data</span>; <span class="comment">// Array of todos</span>
};</code></pre>
                        </div>
                    </div>

                    <!-- Create Todo Example -->
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Create a New Todo</span>
                            <div class="code-dots">
                                <span class="dot red"></span>
                                <span class="dot yellow"></span>
                                <span class="dot green"></span>
                            </div>
                        </div>
                        <div class="code-content">
                            <pre><code><span class="keyword">const</span> <span class="function">createTodo</span> = <span class="keyword">async</span> (todoData) => {
  <span class="keyword">const</span> token = <span class="function">localStorage</span>.<span class="function">getItem</span>(<span class="string">'auth_token'</span>);
  
  <span class="keyword">const</span> response = <span class="keyword">await</span> <span class="function">fetch</span>(<span class="string">'http://localhost:8000/api/todos'</span>, {
    <span class="keyword">method</span>: <span class="string">'POST'</span>,
    <span class="keyword">headers</span>: {
      <span class="string">'Authorization'</span>: <span class="string">`Bearer ${token}`</span>,
      <span class="string">'Content-Type'</span>: <span class="string">'application/json'</span>
    },
    <span class="keyword">body</span>: <span class="function">JSON</span>.<span class="function">stringify</span>({
      <span class="keyword">todo_list_id</span>: <span class="number">1</span>,
      <span class="keyword">title</span>: <span class="string">'Complete project'</span>,
      <span class="keyword">description</span>: <span class="string">'Finish the project documentation'</span>,
      <span class="keyword">priority</span>: <span class="string">'high'</span>,
      <span class="keyword">due_date</span>: <span class="string">'2024-12-31T23:59:59Z'</span>
    })
  });

  <span class="keyword">const</span> data = <span class="keyword">await</span> response.<span class="function">json</span>();
  <span class="keyword">return</span> data.<span class="keyword">data</span>; <span class="comment">// Created todo object</span>
};</code></pre>
                        </div>
                    </div>

                    <!-- Error Handling Example -->
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Error Handling</span>
                            <div class="code-dots">
                                <span class="dot red"></span>
                                <span class="dot yellow"></span>
                                <span class="dot green"></span>
                            </div>
                        </div>
                        <div class="code-content">
                            <pre><code><span class="keyword">const</span> <span class="function">makeApiCall</span> = <span class="keyword">async</span> (url, options) => {
  <span class="keyword">try</span> {
    <span class="keyword">const</span> response = <span class="keyword">await</span> <span class="function">fetch</span>(url, options);
    <span class="keyword">const</span> data = <span class="keyword">await</span> response.<span class="function">json</span>();
    
    <span class="keyword">if</span> (!response.<span class="function">ok</span>) {
      <span class="comment">// Handle error response</span>
      <span class="function">console</span>.<span class="function">error</span>(<span class="string">'API Error:'</span>, data.<span class="keyword">message</span>);
      <span class="keyword">throw</span> <span class="keyword">new</span> <span class="function">Error</span>(data.<span class="keyword">message</span> || <span class="string">'Something went wrong'</span>);
    }
    
    <span class="keyword">return</span> data;
  } <span class="keyword">catch</span> (error) {
    <span class="function">console</span>.<span class="function">error</span>(<span class="string">'Request failed:'</span>, error);
    <span class="keyword">throw</span> error;
  }
};</code></pre>
                        </div>
                    </div>
                </div>

                <div class="info-box">
                    <h4>💡 Pro Tip</h4>
                    <p>Always include the <strong>Authorization: Bearer {token}</strong> header in your requests for protected endpoints. Store your token securely (localStorage, sessionStorage, or state management) and handle token expiration gracefully.</p>
                </div>
            </section>

            <!-- Features Section -->
            <section class="section">
                <h2 class="section-title">✨ API Features</h2>
                <p style="font-size: 1.1rem; color: #6b7280; margin-bottom: 30px;">
                    The Todo API provides a comprehensive set of features for managing todos, lists, and collaboration.
                </p>

                <div class="features">
                    <div class="feature-card">
                        <div class="feature-icon">🔐</div>
                        <h4>Secure Authentication</h4>
                        <p>Token-based authentication using Laravel Sanctum. Simple and secure.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📝</div>
                        <h4>Todo Management</h4>
                        <p>Full CRUD operations with priorities, due dates, and image uploads.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📋</div>
                        <h4>Todo Lists</h4>
                        <p>Organize todos into lists with favorites and archiving support.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🏷️</div>
                        <h4>Tags & Categories</h4>
                        <p>Organize todos with custom tags and colors for better categorization.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💬</div>
                        <h4>Comments & Replies</h4>
                        <p>Add comments to todos with nested reply support for collaboration.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">👥</div>
                        <h4>Sharing & Collaboration</h4>
                        <p>Share todo lists with others with different permission levels.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h4>Statistics & Analytics</h4>
                        <p>Get insights on completion rates, priorities, and overdue items.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h4>Bulk Operations</h4>
                        <p>Update, delete, or assign tags to multiple todos at once.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📤</div>
                        <h4>Export/Import</h4>
                        <p>Backup and restore your data in JSON format.</p>
                    </div>
                </div>
            </section>

            <!-- Documentation Link Section -->
            <section class="section" style="text-align: center; padding: 60px 20px; background: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%); border-radius: 20px; margin-top: 40px;">
                <h2 class="section-title" style="margin-bottom: 20px;">📚 Ready to Explore?</h2>
                <p style="font-size: 1.2rem; color: #6b7280; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    Check out the complete API documentation with interactive examples, request/response schemas, and detailed endpoint descriptions.
                </p>
                <a href="/api-docs" class="cta-button" style="background: var(--bg-gradient); color: white; font-size: 1.2rem; padding: 18px 50px;">
                    View Full API Documentation →
                </a>
            </section>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <p>Built with ❤️ for frontend developers learning API integration</p>
            <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.8;">Todo API v1.0.0</p>
        </div>
    </div>
</body>
</html>
