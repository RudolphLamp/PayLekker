/**
 * Flappy Bird Mini Game for PayLekker
 * Integrated with the game reward system
 */

class FlappyBirdGame {
    constructor() {
        this.canvas = document.getElementById('flappy-canvas');
        this.ctx = this.canvas.getContext('2d');
        this.gameSystem = null; // Will be set by the main game system
        
        // Game state
        this.gameState = 'waiting'; // waiting, playing, gameOver
        this.score = 0;
        this.highScore = parseInt(localStorage.getItem('flappy-high-score') || '0');
        
        // Bird properties
        this.bird = {
            x: 100,
            y: 200,
            width: 30,
            height: 30,
            velocity: 0,
            gravity: 0.4,
            jump: -6.5,
            color: '#FFC107'
        };
        
        // Pipes
        this.pipes = [];
        this.pipeWidth = 60;
        this.pipeGap = 150;
        this.pipeSpeed = 2;
        this.pipeSpawnTimer = 0;
        this.pipeSpawnInterval = 120; // frames
        
        // Game loop
        this.animationId = null;
        this.lastTime = 0;
        
        this.init();
    }
    
    init() {
        this.updateHighScoreDisplay();
        this.setupEventListeners();
        this.resizeCanvas();
        this.gameLoop(0);
        
        // Load mini game challenges when the tab is active
        document.querySelector('[data-tab="minigame"]').addEventListener('click', () => {
            if (this.gameSystem) {
                this.gameSystem.loadMiniGameChallenges().catch(error => {
                    console.warn('Could not load mini game challenges:', error);
                    // Continue without challenges - game will still work
                });
            }
        });
    }
    
    setupEventListeners() {
        // Start game button
        document.getElementById('start-game-btn').addEventListener('click', () => {
            this.startGame();
        });
        
        // Restart game button
        document.getElementById('restart-game-btn').addEventListener('click', () => {
            this.startGame();
        });
        
        // Canvas click
        this.canvas.addEventListener('click', () => {
            if (this.gameState === 'playing') {
                this.flap();
            }
        });
        
        // Keyboard controls
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space' && this.gameState === 'playing') {
                e.preventDefault();
                this.flap();
            }
        });
        
        // Window resize
        window.addEventListener('resize', () => {
            this.resizeCanvas();
        });
    }
    
    resizeCanvas() {
        const container = this.canvas.parentElement;
        const maxWidth = container.clientWidth - 20;
        const maxHeight = 400;
        
        // Maintain aspect ratio
        if (maxWidth < 800) {
            this.canvas.style.width = maxWidth + 'px';
            this.canvas.style.height = (maxWidth * 0.5) + 'px';
        } else {
            this.canvas.style.width = '800px';
            this.canvas.style.height = '400px';
        }
    }
    
    startGame() {
        this.gameState = 'playing';
        this.score = 0;
        this.bird.y = 200;
        this.bird.velocity = 0;
        this.pipes = [];
        this.pipeSpawnTimer = 0;
        
        document.getElementById('start-screen').style.display = 'none';
        document.getElementById('game-over-screen').style.display = 'none';
        document.getElementById('game-overlay').style.display = 'none';
        
        this.updateScoreDisplay();
    }
    
    flap() {
        this.bird.velocity = this.bird.jump;
    }
    
    update() {
        if (this.gameState !== 'playing') return;
        
        // Update bird
        this.bird.velocity += this.bird.gravity;
        this.bird.y += this.bird.velocity;
        
        // Check boundaries
        if (this.bird.y < 0 || this.bird.y + this.bird.height > this.canvas.height) {
            this.gameOver();
            return;
        }
        
        // Spawn pipes
        this.pipeSpawnTimer++;
        if (this.pipeSpawnTimer >= this.pipeSpawnInterval) {
            this.spawnPipe();
            this.pipeSpawnTimer = 0;
        }
        
        // Update pipes
        for (let i = this.pipes.length - 1; i >= 0; i--) {
            const pipe = this.pipes[i];
            pipe.x -= this.pipeSpeed;
            
            // Remove pipes that are off screen
            if (pipe.x + this.pipeWidth < 0) {
                this.pipes.splice(i, 1);
                continue;
            }
            
            // Check collision
            if (this.checkCollision(this.bird, pipe)) {
                this.gameOver();
                return;
            }
            
            // Check if bird passed pipe (score)
            if (!pipe.passed && pipe.x + this.pipeWidth < this.bird.x) {
                pipe.passed = true;
                this.score++;
                this.updateScoreDisplay();
                
                // Check for challenge completion
                this.checkChallengeCompletion();
            }
        }
    }
    
    spawnPipe() {
        const minHeight = 50;
        const maxHeight = this.canvas.height - this.pipeGap - minHeight;
        const topHeight = Math.random() * (maxHeight - minHeight) + minHeight;
        
        this.pipes.push({
            x: this.canvas.width,
            topHeight: topHeight,
            bottomY: topHeight + this.pipeGap,
            bottomHeight: this.canvas.height - (topHeight + this.pipeGap),
            passed: false
        });
    }
    
    checkCollision(bird, pipe) {
        // Check collision with top pipe
        if (bird.x < pipe.x + this.pipeWidth &&
            bird.x + bird.width > pipe.x &&
            bird.y < pipe.topHeight) {
            return true;
        }
        
        // Check collision with bottom pipe
        if (bird.x < pipe.x + this.pipeWidth &&
            bird.x + bird.width > pipe.x &&
            bird.y + bird.height > pipe.bottomY) {
            return true;
        }
        
        return false;
    }
    
    gameOver() {
        this.gameState = 'gameOver';
        
        // Update high score
        if (this.score > this.highScore) {
            this.highScore = this.score;
            localStorage.setItem('flappy-high-score', this.highScore.toString());
            this.updateHighScoreDisplay();
        }
        
        // Show game over screen
        document.getElementById('final-score').textContent = this.score;
        document.getElementById('game-over-screen').style.display = 'block';
        document.getElementById('game-overlay').style.display = 'flex';
        
        // Check for rewards
        this.checkForRewards();
    }
    
    checkForRewards() {
        let rewardMessage = '';
        let rewardAmount = 0;
        
        // Score-based rewards
        if (this.score >= 10) {
            rewardAmount = Math.floor(this.score / 10) * 0.50; // R0.50 for every 10 points
            rewardMessage = `Congratulations! You earned R${rewardAmount.toFixed(2)} for scoring ${this.score} points!`;
            
            // Add money to account through game system
            if (this.gameSystem) {
                this.gameSystem.addMiniGameReward(rewardAmount, `Flappy Bird Score: ${this.score}`);
            }
        }
        
        // High score bonus
        if (this.score === this.highScore && this.score >= 20) {
            const bonus = 1.00;
            rewardAmount += bonus;
            rewardMessage += ` Plus R${bonus.toFixed(2)} new high score bonus!`;
            
            if (this.gameSystem) {
                this.gameSystem.addMiniGameReward(bonus, `Flappy Bird High Score: ${this.score}`);
            }
        }
        
        if (rewardMessage) {
            document.getElementById('reward-message').textContent = rewardMessage;
            document.getElementById('reward-message').style.display = 'block';
        } else {
            document.getElementById('reward-message').style.display = 'none';
        }
    }
    
    checkChallengeCompletion() {
        if (!this.gameSystem) return;
        
        // Check various score milestones for challenges
        const milestones = [5, 10, 15, 20, 30, 50];
        if (milestones.includes(this.score)) {
            this.gameSystem.checkMiniGameChallengeCompletion('score', this.score);
        }
    }
    
    draw() {
        // Clear canvas
        this.ctx.fillStyle = '#87CEEB'; // Sky blue background
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Draw ground
        this.ctx.fillStyle = '#90EE90';
        this.ctx.fillRect(0, this.canvas.height - 50, this.canvas.width, 50);
        
        if (this.gameState === 'waiting') return;
        
        // Draw pipes
        this.ctx.fillStyle = '#228B22';
        this.pipes.forEach(pipe => {
            // Top pipe
            this.ctx.fillRect(pipe.x, 0, this.pipeWidth, pipe.topHeight);
            // Bottom pipe
            this.ctx.fillRect(pipe.x, pipe.bottomY, this.pipeWidth, pipe.bottomHeight);
            
            // Pipe caps
            this.ctx.fillStyle = '#32CD32';
            this.ctx.fillRect(pipe.x - 5, pipe.topHeight - 20, this.pipeWidth + 10, 20);
            this.ctx.fillRect(pipe.x - 5, pipe.bottomY, this.pipeWidth + 10, 20);
            this.ctx.fillStyle = '#228B22';
        });
        
        // Draw bird
        this.ctx.fillStyle = this.bird.color;
        this.ctx.fillRect(this.bird.x, this.bird.y, this.bird.width, this.bird.height);
        
        // Bird details (simple)
        this.ctx.fillStyle = '#FF4500';
        this.ctx.fillRect(this.bird.x + 20, this.bird.y + 10, 8, 4); // beak
        this.ctx.fillStyle = '#000';
        this.ctx.fillRect(this.bird.x + 5, this.bird.y + 8, 3, 3); // eye
    }
    
    gameLoop(currentTime) {
        this.update();
        this.draw();
        
        this.animationId = requestAnimationFrame((time) => this.gameLoop(time));
    }
    
    updateScoreDisplay() {
        document.getElementById('current-score').textContent = this.score;
    }
    
    updateHighScoreDisplay() {
        document.getElementById('high-score').textContent = this.highScore;
    }
    
    destroy() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
    }
}

// Initialize game when DOM is ready
let flappyGame = null;

document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for other scripts to load
    setTimeout(() => {
        if (document.getElementById('flappy-canvas')) {
            flappyGame = new FlappyBirdGame();
            
            // Connect to game system when it's available
            if (window.gameSystem) {
                flappyGame.gameSystem = window.gameSystem;
            } else {
                // Wait for game system to initialize
                const checkGameSystem = setInterval(() => {
                    if (window.gameSystem) {
                        flappyGame.gameSystem = window.gameSystem;
                        clearInterval(checkGameSystem);
                    }
                }, 100);
            }
        }
    }, 500);
});