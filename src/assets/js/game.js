/**
 * PayLekker Ga    init() {
        if (!this.token) {
            console.log('No auth token found, redirecting to login');
            // Redirect to the landing page with login form
            window.location.href = 'index.php';
            return;
        }avaScript
 * Handles game UI interactions, API calls, and reward animations
 */

class GameSystem {
    constructor() {
        // Check both storage locations for the token (maintain compatibility)
        this.token = sessionStorage.getItem('auth_token') || localStorage.getItem('authToken');
        this.baseUrl = 'game.php';
        this.currentTab = 'challenges';
        this.challenges = [];
        this.progress = {};
        this.rewards = [];
        this.achievements = [];
        
        this.init();
    }
    
    init() {
        if (!this.token) {
            console.log('No auth token found, redirecting to login');
            // Redirect to the landing page with login form
            window.location.href = 'index.php';
            return;
        }
        
        console.log('Game system initializing with token:', this.token ? 'Present' : 'Missing');
        
        // Ensure DOM is ready before setting up event listeners
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupEventListeners();
                this.loadInitialData();
            });
        } else {
            this.setupEventListeners();
            this.loadInitialData();
        }
    }
    
    setupEventListeners() {
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-btn').dataset.tab;
                this.switchTab(tab);
            });
        });
        
        // Challenge filtering
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const filter = e.target.dataset.filter;
                this.filterChallenges(filter);
                
                // Update active filter button
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
            });
        });
        
        // Modal controls
        document.getElementById('modal-close').addEventListener('click', () => {
            this.hideModal('challenge-modal');
        });
        
        document.getElementById('modal-cancel').addEventListener('click', () => {
            this.hideModal('challenge-modal');
        });
        
        document.getElementById('modal-complete').addEventListener('click', () => {
            console.log('Modal complete button clicked');
            this.completeChallengeFromModal();
        });
        
        document.getElementById('celebration-close').addEventListener('click', () => {
            this.hideModal('celebration-modal');
        });
    }
    
    async loadInitialData() {
        this.showLoading(true);
        
        // Safety timeout to hide loading overlay if something goes wrong
        const safetyTimeout = setTimeout(() => {
            console.warn('Loading timeout - force hiding overlay');
            this.showLoading(false);
        }, 10000);
        
        try {
            // Load user profile data first
            await this.loadUserProfile();
            
            await Promise.all([
                this.loadProgress(),
                this.loadChallenges(),
                this.loadRewards(),
                this.loadAchievements()
            ]);
            
            this.renderProgress();
            this.renderChallenges();
            this.renderRewards();
            this.renderAchievements();
        } catch (error) {
            console.error('Error loading initial data:', error);
            this.showNotification('Error loading game data. Please refresh the page.', 'error');
        } finally {
            clearTimeout(safetyTimeout);
            this.showLoading(false);
        }
    }
    
    async apiCall(endpoint, method = 'GET', data = null) {
        const url = `${this.baseUrl}?action=${endpoint}`;
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.token}`
            }
        };
        
        if (data) {
            // Include token in the request body as a fallback
            data.auth_token = this.token;
            options.body = JSON.stringify(data);
        } else if (method === 'POST') {
            // For POST without data, send token in body
            options.body = JSON.stringify({ auth_token: this.token });
        }
        
        console.log('API Call:', method, endpoint, 'Token length:', this.token ? this.token.length : 0);
        
        try {
            const response = await fetch(url, options);
            
            // Check if the response is not ok (401, 403, etc.)
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    // Authentication failed, redirect to login
                    sessionStorage.removeItem('auth_token');
                    localStorage.removeItem('authToken');
                    window.location.href = 'index.php';
                    return;
                }
                
                // Try to get error message from response body
                let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                try {
                    const errorData = await response.json();
                    if (errorData.error) {
                        errorMessage = errorData.error;
                        if (errorData.debug) {
                            console.error('Server debug info:', errorData.debug);
                        }
                    }
                } catch (e) {
                    // If we can't parse JSON, stick with status text
                }
                
                throw new Error(errorMessage);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                // If the error indicates authentication failure
                if (result.error && result.error.includes('token')) {
                    sessionStorage.removeItem('auth_token');
                    localStorage.removeItem('authToken');
                    window.location.href = 'index.php';
                    return;
                }
                throw new Error(result.error || 'API call failed');
            }
            
            return result;
        } catch (error) {
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                throw new Error('Network error. Please check your connection.');
            }
            throw error;
        }
    }
    
    async loadUserProfile() {
        try {
            // Check if we have cached user data first
            const userData = sessionStorage.getItem('user_data');
            if (userData) {
                try {
                    const user = JSON.parse(userData);
                    this.updateUserInfo(user);
                } catch (e) {
                    console.error('Failed to parse cached user data:', e);
                }
            }
            
            // Load fresh profile data
            const response = await fetch('profile.php', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data && result.data.user) {
                    const user = result.data.user;
                    // Cache the user data
                    sessionStorage.setItem('user_data', JSON.stringify(user));
                    this.updateUserInfo(user);
                }
            }
        } catch (error) {
            console.error('Error loading user profile:', error);
            // Try to use cached data if available
            const userData = sessionStorage.getItem('user_data');
            if (userData) {
                try {
                    const user = JSON.parse(userData);
                    this.updateUserInfo(user);
                } catch (e) {
                    console.error('Failed to use cached user data:', e);
                }
            }
        }
    }
    
    updateUserInfo(user) {
        try {
            // Update user name in navigation
            const userNameNav = document.getElementById('userNameNav');
            if (userNameNav) {
                userNameNav.textContent = `${user.first_name} ${user.last_name}`;
            }
            
            // Update account balance in progress card
            const accountBalance = document.getElementById('account-balance');
            if (accountBalance) {
                const balance = parseFloat(user.account_balance.toString().replace(/[^\d.-]/g, '')) || 0;
                accountBalance.textContent = `R${balance.toLocaleString('en-ZA', {
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2
                })}`;
            }
            
            // Update user initials
            const userInitials = document.getElementById('userInitials');
            if (userInitials && user.first_name && user.last_name) {
                userInitials.textContent = `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`;
            }
        } catch (error) {
            console.error('Error updating user info:', error);
        }
    }

    async loadProgress() {
        try {
            const result = await this.apiCall('progress');
            this.progress = result.progress;
        } catch (error) {
            console.error('Error loading progress:', error);
        }
    }
    
    async loadChallenges() {
        try {
            const result = await this.apiCall('challenges');
            this.challenges = result.challenges;
        } catch (error) {
            console.error('Error loading challenges:', error);
        }
    }
    
    async loadRewards() {
        try {
            const result = await this.apiCall('rewards');
            this.rewards = result.rewards;
        } catch (error) {
            console.error('Error loading rewards:', error);
        }
    }
    
    async loadAchievements() {
        try {
            const result = await this.apiCall('achievements');
            this.achievements = result.achievements;
        } catch (error) {
            console.error('Error loading achievements:', error);
        }
    }
    
    renderProgress() {
        if (!this.progress) return;
        
        // Update elements that exist
        const userLevel = document.getElementById('user-level');
        if (userLevel) userLevel.textContent = this.progress.level || 1;
        
        const totalPoints = document.getElementById('total-points');
        if (totalPoints) totalPoints.textContent = this.progress.total_points || 0;
        
        const currentStreak = document.getElementById('current-streak');
        if (currentStreak) currentStreak.textContent = this.progress.current_streak || 0;
        
        // Update account balance
        const accountBalance = document.getElementById('account-balance');
        if (accountBalance) {
            accountBalance.textContent = `R${parseFloat(this.progress.account_balance || 0).toFixed(2)}`;
        }
        
        // Update XP bar
        const xpFill = document.getElementById('xp-fill');
        const xpText = document.getElementById('xp-text');
        const progressPercent = this.progress.progress_percentage || 0;
        
        if (xpFill) xpFill.style.width = `${progressPercent}%`;
        if (xpText) xpText.textContent = `${this.progress.experience_progress || 0} / ${this.progress.experience_for_current_level || 100} XP`;
    }
    
    renderChallenges() {
        const grid = document.getElementById('challenges-grid');
        grid.innerHTML = '';
        
        this.challenges.forEach(challenge => {
            const card = this.createChallengeCard(challenge);
            grid.appendChild(card);
        });
    }
    
    createChallengeCard(challenge) {
        const card = document.createElement('div');
        card.className = `challenge-card ${challenge.completed_today ? 'completed' : ''}`;
        
        const difficultyClass = `difficulty-${challenge.difficulty}`;
        const typeIcon = this.getChallengeTypeIcon(challenge.challenge_type);
        
        card.innerHTML = `
            <div class="challenge-header">
                <div class="challenge-title">
                    <h3>${challenge.title}</h3>
                    <span class="challenge-difficulty ${difficultyClass}">${challenge.difficulty}</span>
                </div>
                <div class="challenge-type">
                    <i class="${typeIcon}"></i>
                    ${challenge.challenge_type.replace('_', ' ')}
                </div>
            </div>
            <div class="challenge-body">
                <div class="challenge-description">
                    ${challenge.description}
                </div>
                <div class="challenge-rewards">
                    ${challenge.points_reward ? `<div class="reward-item points"><i class="bi bi-star-fill"></i> ${challenge.points_reward} points</div>` : ''}
                    ${challenge.money_reward ? `<div class="reward-item money"><i class="bi bi-cash-coin"></i> R${challenge.money_reward}</div>` : ''}
                    ${challenge.free_transactions ? `<div class="reward-item transactions"><i class="bi bi-gift"></i> ${challenge.free_transactions} free transactions</div>` : ''}
                </div>
                <div class="challenge-actions">
                    ${challenge.completed_today 
                        ? '<div class="completed-badge"><i class="bi bi-check-circle-fill"></i> Completed!</div>'
                        : `<button class="btn btn-primary" onclick="gameSystem.openChallengeModal(${challenge.id})">
                             <i class="bi bi-play-fill"></i> Start Challenge
                           </button>`
                    }
                </div>
            </div>
        `;
        
        return card;
    }
    
    renderRewards() {
        const section = document.getElementById('rewards-section');
        const grid = document.getElementById('rewards-grid');
        
        if (this.rewards.length === 0) {
            section.style.display = 'none';
            return;
        }
        
        section.style.display = 'block';
        grid.innerHTML = '';
        
        this.rewards.forEach(reward => {
            const card = this.createRewardCard(reward);
            grid.appendChild(card);
        });
    }
    
    createRewardCard(reward) {
        const card = document.createElement('div');
        card.className = 'reward-card';
        
        const typeIcon = this.getRewardTypeIcon(reward.reward_type);
        const value = reward.reward_type === 'money' 
            ? `R${parseFloat(reward.reward_value).toFixed(2)}`
            : `${reward.reward_value}`;
        
        card.innerHTML = `
            <div class="reward-header">
                <div class="reward-type">
                    <i class="${typeIcon}"></i>
                    ${reward.reward_type.replace('_', ' ')}
                </div>
                <div class="reward-value">${value}</div>
            </div>
            <div class="reward-description">${reward.description}</div>
            <div class="reward-actions">
                <button class="btn btn-success" onclick="gameSystem.claimReward(${reward.id})">
                    <i class="bi bi-gift"></i> Claim
                </button>
            </div>
        `;
        
        return card;
    }
    
    renderAchievements() {
        const grid = document.getElementById('achievements-grid');
        
        if (this.achievements.length === 0) {
            grid.innerHTML = '<div class="no-achievements"><p>No achievements yet. Complete challenges to unlock achievements!</p></div>';
            return;
        }
        
        grid.innerHTML = '';
        
        this.achievements.forEach(achievement => {
            const card = this.createAchievementCard(achievement);
            grid.appendChild(card);
        });
    }
    
    createAchievementCard(achievement) {
        const card = document.createElement('div');
        card.className = 'achievement-card';
        
        // Map achievement types to emojis
        const achievementEmojis = {
            'first_login': '👋',
            'first_transaction': '💳',
            'first_deposit': '💰',
            'frequent_user': '⭐',
            'big_spender': '💎',
            'savings_master': '🏦',
            'challenge_completed': '🏆',
            'game_master': '🎮',
            'streak_keeper': '🔥',
            'budget_guru': '📊',
            'transfer_expert': '🔄',
            'milestone_reached': '🎯',
            'loyalty_badge': '👑',
            'achievement_hunter': '🏅',
            'default': '🏆'
        };
        
        // Get emoji based on achievement name or description
        let emoji = achievementEmojis['default'];
        const achievementName = achievement.achievement_name?.toLowerCase() || '';
        const achievementDesc = achievement.achievement_description?.toLowerCase() || '';
        
        // Check for specific keywords to assign appropriate emojis
        if (achievementName.includes('login') || achievementDesc.includes('login')) {
            emoji = achievementEmojis['first_login'];
        } else if (achievementName.includes('transaction') || achievementDesc.includes('transaction')) {
            emoji = achievementEmojis['first_transaction'];
        } else if (achievementName.includes('deposit') || achievementDesc.includes('deposit')) {
            emoji = achievementEmojis['first_deposit'];
        } else if (achievementName.includes('game') || achievementDesc.includes('game')) {
            emoji = achievementEmojis['game_master'];
        } else if (achievementName.includes('challenge') || achievementDesc.includes('challenge')) {
            emoji = achievementEmojis['challenge_completed'];
        } else if (achievementName.includes('budget') || achievementDesc.includes('budget')) {
            emoji = achievementEmojis['budget_guru'];
        } else if (achievementName.includes('transfer') || achievementDesc.includes('transfer')) {
            emoji = achievementEmojis['transfer_expert'];
        } else if (achievementName.includes('streak') || achievementDesc.includes('streak')) {
            emoji = achievementEmojis['streak_keeper'];
        } else if (achievementName.includes('savings') || achievementDesc.includes('savings')) {
            emoji = achievementEmojis['savings_master'];
        }
        
        card.innerHTML = `
            <div class="achievement-icon">
                <div class="emoji">${emoji}</div>
            </div>
            <div class="achievement-name">${achievement.achievement_name}</div>
            <div class="achievement-description">${achievement.achievement_description}</div>
            <div class="achievement-date">
                Unlocked on ${new Date(achievement.unlocked_at).toLocaleDateString()}
            </div>
        `;
        
        return card;
    }
    
    switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tabName);
        });
        
        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.toggle('active', content.id === `${tabName}-tab`);
        });
        
        this.currentTab = tabName;
    }
    
    filterChallenges(filter) {
        const cards = document.querySelectorAll('.challenge-card');
        
        cards.forEach(card => {
            if (filter === 'all') {
                card.style.display = 'block';
            } else {
                const typeElement = card.querySelector('.challenge-type');
                const type = typeElement.textContent.trim().toLowerCase().replace(' ', '_');
                card.style.display = type === filter ? 'block' : 'none';
            }
        });
    }
    
    openChallengeModal(challengeId) {
        console.log('Opening challenge modal for challenge:', challengeId);
        const challenge = this.challenges.find(c => c.id == challengeId);
        if (!challenge) return;
        
        document.getElementById('modal-title').textContent = `Complete: ${challenge.title}`;
        document.getElementById('modal-description').textContent = challenge.description;
        
        // Create form fields based on challenge requirements
        const formContainer = document.getElementById('modal-form');
        formContainer.innerHTML = this.createChallengeForm(challenge);
        
        // Show reward preview
        const rewardsContainer = document.getElementById('modal-rewards');
        rewardsContainer.innerHTML = `
            <h4>Rewards:</h4>
            <div class="reward-preview">
                ${challenge.points_reward ? `<div class="reward-item points"><i class="bi bi-star-fill"></i> ${challenge.points_reward} points</div>` : ''}
                ${challenge.money_reward ? `<div class="reward-item money"><i class="bi bi-cash-coin"></i> R${challenge.money_reward}</div>` : ''}
                ${challenge.free_transactions ? `<div class="reward-item transactions"><i class="bi bi-gift"></i> ${challenge.free_transactions} free transactions</div>` : ''}
            </div>
        `;
        
        // Store challenge ID for completion
        document.getElementById('modal-complete').dataset.challengeId = challengeId;
        
        this.showModal('challenge-modal');
    }
    
    createChallengeForm(challenge) {
        const requirements = challenge.requirements || {};
        let formHtml = '';
        
        if (requirements.min_transactions) {
            formHtml += `
                <div class="form-group">
                    <label for="transactions">Number of transactions completed:</label>
                    <input type="number" id="transactions" name="transactions" min="${requirements.min_transactions}" required>
                    <small>Minimum: ${requirements.min_transactions}</small>
                </div>
            `;
        }
        
        if (requirements.min_amount) {
            formHtml += `
                <div class="form-group">
                    <label for="amount">Total transaction amount (R):</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="${requirements.min_amount}" required>
                    <small>Minimum: R${requirements.min_amount}</small>
                </div>
            `;
        }
        
        if (requirements.unique_recipients) {
            formHtml += `
                <div class="form-group">
                    <label for="recipients">Number of unique recipients:</label>
                    <input type="number" id="recipients" name="recipients" min="${requirements.unique_recipients}" required>
                    <small>Minimum: ${requirements.unique_recipients}</small>
                </div>
            `;
        }
        
        // Default completion confirmation if no specific requirements
        if (formHtml === '') {
            formHtml = `
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="confirm" name="confirm" required>
                        I confirm that I have completed this challenge
                    </label>
                </div>
            `;
        }
        
        return formHtml;
    }
    
    async completeChallengeFromModal() {
        const challengeId = document.getElementById('modal-complete').dataset.challengeId;
        const formContainer = document.getElementById('modal-form');
        const completionData = {};
        
        // Manually collect form data from inputs within the modal-form container
        const inputs = formContainer.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name) {
                if (input.type === 'checkbox') {
                    completionData[input.name] = input.checked;
                } else {
                    completionData[input.name] = input.value;
                }
            }
        });
        
        console.log('Completion data collected:', JSON.stringify(completionData));
        
        try {
            this.showLoading(true);
            
            const result = await this.apiCall('complete_challenge', 'POST', {
                challenge_id: challengeId,
                completion_data: completionData
            });
            
            console.log('Challenge completion result:', result);
            
            this.hideModal('challenge-modal');
            this.showCelebration(result.rewards);
            
            // Refresh data
            await this.loadInitialData();
            
        } catch (error) {
            console.error('Error completing challenge:', error);
            this.showNotification(error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }
    
    async claimReward(rewardId) {
        try {
            this.showLoading(true);
            
            const result = await this.apiCall('claim_reward', 'POST', {
                reward_id: rewardId
            });
            
            this.showNotification(`Reward claimed: ${result.reward.description}`, 'success');
            
            // Refresh rewards and progress
            await Promise.all([
                this.loadRewards(),
                this.loadProgress()
            ]);
            
            this.renderRewards();
            this.renderProgress();
            
        } catch (error) {
            console.error('Error claiming reward:', error);
            this.showNotification(error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }
    
    showCelebration(rewards) {
        const message = document.getElementById('celebration-message');
        const rewardsContainer = document.getElementById('celebration-rewards');
        
        message.textContent = 'Challenge completed successfully!';
        
        let rewardsHtml = '';
        if (rewards.points > 0) {
            rewardsHtml += `<div class="celebration-reward"><i class="bi bi-star-fill"></i> +${rewards.points} Points</div>`;
        }
        if (rewards.money > 0) {
            rewardsHtml += `<div class="celebration-reward"><i class="bi bi-cash-coin"></i> +R${rewards.money}</div>`;
        }
        if (rewards.free_transactions > 0) {
            rewardsHtml += `<div class="celebration-reward"><i class="bi bi-gift"></i> +${rewards.free_transactions} Free Transactions</div>`;
        }
        
        rewardsContainer.innerHTML = rewardsHtml;
        this.showModal('celebration-modal');
    }
    
    showModal(modalId) {
        // Clean up any existing modal instances
        const existingModal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (existingModal) {
            existingModal.dispose();
        }
        
        const modalElement = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        modal.show();
        
        // Store modal instance for cleanup
        this.currentModal = modal;
    }
    
    hideModal(modalId) {
        const modalElement = document.getElementById(modalId);
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        this.currentModal = null;
    }
    
    showLoading(show) {
        document.getElementById('loading-overlay').style.display = show ? 'flex' : 'none';
    }
    
    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        const icon = type === 'success' ? 'bi bi-check-circle-fill' :
                     type === 'error' ? 'bi bi-exclamation-circle-fill' :
                     type === 'warning' ? 'bi bi-exclamation-triangle-fill' :
                     'bi bi-info-circle-fill';
        
        notification.innerHTML = `
            <i class="${icon}"></i>
            <span>${message}</span>
        `;
        
        container.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
    
    getChallengeTypeIcon(type) {
        const icons = {
            'daily': 'bi bi-sun',
            'weekly': 'bi bi-calendar-week',
            'one_time': 'bi bi-star',
            'milestone': 'bi bi-mountain'
        };
        return icons[type] || 'bi bi-list-task';
    }
    
    getRewardTypeIcon(type) {
        const icons = {
            'money': 'bi bi-currency-dollar',
            'free_transactions': 'bi bi-gift',
            'points': 'bi bi-star',
            'badge': 'bi bi-award'
        };
        return icons[type] || 'bi bi-gift';
    }
    
    // Mini Game Integration Methods
    async loadMiniGameChallenges() {
        try {
            // Show loading state
            const container = document.getElementById('mini-game-challenges');
            if (container) {
                container.innerHTML = `
                    <div class="d-flex justify-content-center p-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading challenges...</span>
                        </div>
                    </div>
                `;
            }
            
            const result = await this.apiCall('mini_game_challenges', 'GET');
            if (result && result.challenges) {
                this.renderMiniGameChallenges(result.challenges);
            } else {
                if (container) {
                    container.innerHTML = '<p class="text-muted text-center p-3">No challenges available</p>';
                }
            }
        } catch (error) {
            console.error('Error loading mini game challenges:', error);
            const container = document.getElementById('mini-game-challenges');
            if (container) {
                container.innerHTML = `
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Unable to load challenges. Please try again later.
                    </div>
                `;
            }
        }
    }
    
    renderMiniGameChallenges(challenges) {
        const container = document.getElementById('mini-game-challenges');
        if (!container) return;
        
        container.innerHTML = '';
        
        if (!challenges || challenges.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No challenges available</p>';
            return;
        }
        
        challenges.forEach(challenge => {
            const item = document.createElement('div');
            item.className = `mini-challenge-item ${challenge.completed ? 'completed' : ''}`;
            
            const progress = challenge.current_progress || 0;
            const target = challenge.target_value || 1;
            const progressPercent = Math.min((progress / target) * 100, 100);
            
            item.innerHTML = `
                <div class="mini-challenge-title">
                    <span>${challenge.title}</span>
                    <span class="mini-challenge-reward">R${parseFloat(challenge.money_reward || 0).toFixed(2)}</span>
                </div>
                <div class="mini-challenge-description">${challenge.description}</div>
                <div class="mini-challenge-progress">
                    <div class="mini-challenge-progress-bar" style="width: ${progressPercent}%"></div>
                </div>
                <div class="mini-challenge-status ${challenge.completed ? 'completed' : ''}">
                    ${challenge.completed ? 
                        '<i class="bi bi-check-circle"></i> Completed!' : 
                        `<i class="bi bi-target"></i> Progress: ${progress}/${target}`
                    }
                </div>
            `;
            
            container.appendChild(item);
        });
    }
    
    async addMiniGameReward(amount, reason) {
        try {
            const result = await this.apiCall('mini_game_reward', 'POST', {
                amount: amount,
                reason: reason
            });
            
            if (result && result.success) {
                this.showNotification(`You earned R${amount.toFixed(2)} from ${reason}!`, 'success');
                // Refresh user profile to update balance
                this.loadUserProfile();
                this.loadProgress(); // Refresh progress stats
            }
        } catch (error) {
            console.error('Error adding mini game reward:', error);
        }
    }
    
    async checkMiniGameChallengeCompletion(type, value) {
        try {
            const result = await this.apiCall('check_mini_game_challenge', 'POST', {
                challenge_type: type,
                value: value
            });
            
            if (result && result.completed_challenges) {
                result.completed_challenges.forEach(challenge => {
                    this.showNotification(
                        `Challenge completed: ${challenge.title}! You earned R${challenge.reward_amount}!`, 
                        'success'
                    );
                });
                
                // Refresh challenges and progress
                this.loadMiniGameChallenges();
                this.loadProgress();
                this.loadUserProfile();
            }
        } catch (error) {
            console.error('Error checking mini game challenge completion:', error);
        }
    }
}

// Initialize game system when page loads
let gameSystem;
document.addEventListener('DOMContentLoaded', () => {
    gameSystem = new GameSystem();
    // Make it globally available for mini game integration
    window.gameSystem = gameSystem;
});