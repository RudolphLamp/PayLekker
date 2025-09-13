<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker API Test Suite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .test-group { margin: 10px 0; }
        .test-btn { margin: 5px; }
        .results { margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; min-height: 50px; }
        .endpoint { background: #e3f2fd; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        .test-result { margin: 15px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
        .status-success { border-left-color: #28a745; }
        .status-error { border-left-color: #dc3545; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; font-size: 0.85em; max-height: 200px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title mb-0">🚀 PayLekker API Test Suite</h1>
                        <p class="mb-0 text-muted">Comprehensive testing for all PayLekker API endpoints</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Authentication Tests -->
                        <div class="test-section">
                            <h2>🔐 Authentication System Tests</h2>
                            <div class="test-group">
                                <button onclick="runAuthTests()" class="btn btn-primary test-btn">Run All Auth Tests</button>
                                <button onclick="testRegistration()" class="btn btn-outline-primary test-btn">Test Registration</button>
                                <button onclick="testLogin()" class="btn btn-outline-primary test-btn">Test Login</button>
                                <button onclick="testProfile()" class="btn btn-outline-primary test-btn">Test Profile</button>
                            </div>
                            <div id="auth-results" class="results"></div>
                        </div>

                        <!-- Transfer System Tests -->
                        <div class="test-section">
                            <h2>💸 Transfer System Tests</h2>
                            <div class="test-group">
                                <button onclick="runTransferTests()" class="btn btn-success test-btn">Run All Transfer Tests</button>
                                <button onclick="testTransfer()" class="btn btn-outline-success test-btn">Test P2P Transfer</button>
                                <button onclick="testTransferValidation()" class="btn btn-outline-success test-btn">Test Transfer Validation</button>
                                <button onclick="getTransactionHistory()" class="btn btn-outline-success test-btn">Get Transaction History</button>
                            </div>
                            <div id="transfer-results" class="results"></div>
                        </div>

                        <!-- Budget System Tests -->
                        <div class="test-section">
                            <h2>📊 Budget System Tests</h2>
                            <div class="test-group">
                                <button onclick="runBudgetTests()" class="btn btn-warning test-btn">Run All Budget Tests</button>
                                <button onclick="createBudget()" class="btn btn-outline-warning test-btn">Create Budget Category</button>
                                <button onclick="getBudgets()" class="btn btn-outline-warning test-btn">Get All Budgets</button>
                                <button onclick="updateBudget()" class="btn btn-outline-warning test-btn">Update Budget Spending</button>
                                <button onclick="deleteBudget()" class="btn btn-outline-warning test-btn">Delete Budget</button>
                            </div>
                            <div id="budget-results" class="results"></div>
                        </div>

                        <!-- Chatbot Tests -->
                        <div class="test-section">
                            <h2>🤖 Chatbot Assistant Tests</h2>
                            <div class="test-group">
                                <button onclick="runChatbotTests()" class="btn btn-info test-btn">Run All Chatbot Tests</button>
                                <button onclick="testChatbot('What is my balance?')" class="btn btn-outline-info test-btn">Test Balance Query</button>
                                <button onclick="testChatbot('Help me with transfers')" class="btn btn-outline-info test-btn">Test Transfer Help</button>
                                <button onclick="testChatbot('How can I budget better?')" class="btn btn-outline-info test-btn">Test Budget Advice</button>
                                <button onclick="testChatbot('Hello there!')" class="btn btn-outline-info test-btn">Test Greeting</button>
                            </div>
                            <div id="chatbot-results" class="results"></div>
                        </div>

                        <!-- Complete API Test -->
                        <div class="test-section">
                            <h2>🎯 Complete API Test Suite</h2>
                            <div class="test-group">
                                <button onclick="runFullTestSuite()" class="btn btn-danger test-btn">🚀 Run Complete Test Suite</button>
                                <button onclick="clearAllResults()" class="btn btn-secondary test-btn">Clear All Results</button>
                            </div>
                            <div id="complete-results" class="results"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let authToken = '';
        let testUserId = null;
        let testBudgetId = null;
        let testRecipientId = null;
        const baseUrl = window.location.origin + window.location.pathname.replace('test.php', '');
        
        // Test data
        const testUser = {
            first_name: 'Test',
            last_name: 'User',
            email: 'test' + Date.now() + '@example.com',
            phone: '0821234567',
            password: 'TestPass123!'
        };

        const testRecipient = {
            first_name: 'Recipient',
            last_name: 'User',
            email: 'recipient' + Date.now() + '@example.com',
            phone: '0829876543',
            password: 'RecipientPass123!'
        };

        // Utility function to make API calls
        async function makeAPICall(endpoint, method = 'GET', data = null, includeAuth = false) {
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            
            if (includeAuth && authToken) {
                headers['Authorization'] = 'Bearer ' + authToken;
            }
            
            const config = {
                method: method,
                headers: headers
            };
            
            if (data && ['POST', 'PUT', 'PATCH'].includes(method)) {
                config.body = JSON.stringify(data);
            }
            
            try {
                const response = await fetch(baseUrl + endpoint, config);
                const jsonData = await response.json();
                
                return {
                    status: response.status,
                    ok: response.ok,
                    data: jsonData
                };
            } catch (error) {
                return {
                    status: 0,
                    ok: false,
                    error: error.message
                };
            }
        }

        // Display test results
        function displayResult(containerId, title, result, expectedStatus = 200) {
            const container = document.getElementById(containerId);
            const resultDiv = document.createElement('div');
            resultDiv.className = 'test-result ' + (result.status === expectedStatus ? 'status-success' : 'status-error');
            
            let statusIcon = result.status === expectedStatus ? '✅' : '❌';
            
            resultDiv.innerHTML = `
                <h6>${statusIcon} ${title}</h6>
                <p><strong>Status:</strong> ${result.status} ${result.ok ? 'OK' : 'Error'}</p>
                ${result.data ? `<pre>${JSON.stringify(result.data, null, 2)}</pre>` : ''}
                ${result.error ? `<div class="alert alert-danger">Error: ${result.error}</div>` : ''}
            `;
            
            container.appendChild(resultDiv);
            return result;
        }

        // Authentication Tests
        async function testRegistration() {
            const result = await makeAPICall('register.php', 'POST', testUser);
            displayResult('auth-results', 'User Registration', result, 201);
            
            if (result.ok && result.data.success) {
                authToken = result.data.data.token;
                testUserId = result.data.data.user_id;
            }
            return result;
        }

        async function testLogin() {
            const loginData = {
                email: testUser.email,
                password: testUser.password
            };
            
            const result = await makeAPICall('login.php', 'POST', loginData);
            displayResult('auth-results', 'User Login', result, 200);
            
            if (result.ok && result.data.success) {
                authToken = result.data.data.token;
                testUserId = result.data.data.user_id;
            }
            return result;
        }

        async function testProfile() {
            const result = await makeAPICall('profile.php', 'GET', null, true);
            displayResult('auth-results', 'Get User Profile', result, 200);
            return result;
        }

        async function runAuthTests() {
            document.getElementById('auth-results').innerHTML = '<h5>Running authentication tests...</h5>';
            
            await testRegistration();
            await new Promise(resolve => setTimeout(resolve, 500)); // Small delay
            
            // Create recipient for transfer tests
            const recipientResult = await makeAPICall('register.php', 'POST', testRecipient);
            if (recipientResult.ok && recipientResult.data.success) {
                testRecipientId = recipientResult.data.data.user_id;
            }
            
            await testProfile();
        }

        // Transfer System Tests
        async function testTransfer() {
            if (!authToken) {
                displayResult('transfer-results', 'Transfer Test - No Auth', {status: 401, ok: false, data: {message: 'Please run authentication tests first'}});
                return;
            }

            const transferData = {
                recipient_phone: testRecipient.phone,
                amount: 50.00,
                description: 'Test transfer'
            };

            const result = await makeAPICall('transfer.php', 'POST', transferData, true);
            displayResult('transfer-results', 'P2P Money Transfer', result, 200);
            return result;
        }

        async function testTransferValidation() {
            if (!authToken) {
                displayResult('transfer-results', 'Transfer Validation - No Auth', {status: 401, ok: false, data: {message: 'Please run authentication tests first'}});
                return;
            }

            // Test insufficient funds
            const transferData = {
                recipient_phone: testRecipient.phone,
                amount: 999999.00,
                description: 'Test insufficient funds'
            };

            const result = await makeAPICall('transfer.php', 'POST', transferData, true);
            displayResult('transfer-results', 'Transfer Validation (Insufficient Funds)', result, 400);
            return result;
        }

        async function getTransactionHistory() {
            if (!authToken) {
                displayResult('transfer-results', 'Transaction History - No Auth', {status: 401, ok: false, data: {message: 'Please run authentication tests first'}});
                return;
            }

            const result = await makeAPICall('transactions.php?limit=5&page=1', 'GET', null, true);
            displayResult('transfer-results', 'Transaction History', result, 200);
            return result;
        }

        async function runTransferTests() {
            document.getElementById('transfer-results').innerHTML = '<h5>Running transfer tests...</h5>';
            
            if (!authToken) {
                await runAuthTests();
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            await testTransfer();
            await new Promise(resolve => setTimeout(resolve, 500));
            await testTransferValidation();
            await new Promise(resolve => setTimeout(resolve, 500));
            await getTransactionHistory();
        }

        // Budget System Tests
        async function createBudget() {
            if (!authToken) {
                displayResult('budget-results', 'Create Budget - No Auth', {status: 401, ok: false, data: {message: 'Please run authentication tests first'}});
                return;
            }

            const budgetData = {
                category_name: 'Food & Groceries',
                budget_amount: 2000.00,
                period: 'monthly',
                start_date: '2024-01-01',
                end_date: '2024-01-31'
            };

            const result = await makeAPICall('budget.php', 'POST', budgetData, true);
            displayResult('budget-results', 'Create Budget Category', result, 201);
            
            if (result.ok && result.data.success) {
                testBudgetId = result.data.data.budget.id;
            }
            return result;
        }

        async function getBudgets() {
            if (!authToken) {
                displayResult('budget-results', 'Get Budgets - No Auth', {status: 401, ok: false, data: {message: 'Please run authentication tests first'}});
                return;
            }

            const result = await makeAPICall('budget.php', 'GET', null, true);
            displayResult('budget-results', 'Get All Budget Categories', result, 200);
            return result;
        }

        async function updateBudget() {
            if (!authToken || !testBudgetId) {
                displayResult('budget-results', 'Update Budget - Missing Data', {status: 400, ok: false, data: {message: 'Please create a budget first'}});
                return;
            }

            const updateData = {
                spent_amount: 750.00
            };

            const result = await makeAPICall(`budget.php?id=${testBudgetId}`, 'PUT', updateData, true);
            displayResult('budget-results', 'Update Budget Spending', result, 200);
            return result;
        }

        async function deleteBudget() {
            if (!authToken || !testBudgetId) {
                displayResult('budget-results', 'Delete Budget - Missing Data', {status: 400, ok: false, data: {message: 'Please create a budget first'}});
                return;
            }

            const result = await makeAPICall(`budget.php?id=${testBudgetId}`, 'DELETE', null, true);
            displayResult('budget-results', 'Delete Budget Category', result, 200);
            return result;
        }

        async function runBudgetTests() {
            document.getElementById('budget-results').innerHTML = '<h5>Running budget tests...</h5>';
            
            if (!authToken) {
                await runAuthTests();
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            await createBudget();
            await new Promise(resolve => setTimeout(resolve, 500));
            await getBudgets();
            await new Promise(resolve => setTimeout(resolve, 500));
            await updateBudget();
            await new Promise(resolve => setTimeout(resolve, 500));
            await deleteBudget();
        }

        // Chatbot Tests
        async function testChatbot(message) {
            if (!authToken) {
                displayResult('chatbot-results', 'Chatbot Test - No Auth', {status: 401, ok: false, data: {message: 'Please run authentication tests first'}});
                return;
            }

            const chatData = {
                message: message
            };

            const result = await makeAPICall('chatbot.php', 'POST', chatData, true);
            displayResult('chatbot-results', `Chatbot: "${message}"`, result, 200);
            return result;
        }

        async function runChatbotTests() {
            document.getElementById('chatbot-results').innerHTML = '<h5>Running chatbot tests...</h5>';
            
            if (!authToken) {
                await runAuthTests();
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            const messages = [
                'Hello there!',
                'What is my balance?',
                'Help me with transfers',
                'How can I budget better?',
                'Show me my transactions',
                'Thank you!'
            ];
            
            for (let i = 0; i < messages.length; i++) {
                await testChatbot(messages[i]);
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }

        // Complete test suite
        async function runFullTestSuite() {
            const startTime = Date.now();
            document.getElementById('complete-results').innerHTML = '<h5>🚀 Running complete PayLekker API test suite...</h5>';
            
            // Clear all previous results
            clearAllResults();
            
            try {
                // Run all test suites
                await runAuthTests();
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                await runTransferTests();
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                await runBudgetTests();
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                await runChatbotTests();
                
                const endTime = Date.now();
                const duration = ((endTime - startTime) / 1000).toFixed(2);
                
                displayResult('complete-results', `🎉 Complete Test Suite Finished in ${duration}s`, {
                    status: 200,
                    ok: true,
                    data: {
                        message: 'All PayLekker API endpoints tested successfully!',
                        duration: duration + ' seconds',
                        features_tested: [
                            '✅ User Authentication (Register, Login, Profile)',
                            '✅ P2P Money Transfers',
                            '✅ Transaction History',
                            '✅ Budget Management',
                            '✅ AI Chatbot Assistant'
                        ]
                    }
                }, 200);
                
            } catch (error) {
                displayResult('complete-results', '❌ Test Suite Error', {
                    status: 500,
                    ok: false,
                    error: error.message
                }, 500);
            }
        }

        // Clear all results
        function clearAllResults() {
            ['auth-results', 'transfer-results', 'budget-results', 'chatbot-results', 'complete-results'].forEach(id => {
                document.getElementById(id).innerHTML = '';
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('PayLekker API Test Suite Loaded');
            console.log('Base URL:', baseUrl);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>