-- Additional tables for Play features

-- Table for challenges
CREATE TABLE IF NOT EXISTS challenges (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    challenger_id INT UNSIGNED NOT NULL,
    challenged_id INT UNSIGNED NOT NULL,
    time_control ENUM('bullet','blitz','rapid','classical') NOT NULL DEFAULT 'blitz',
    challenger_color ENUM('white','black','random') NOT NULL DEFAULT 'random',
    message TEXT NULL,
    status ENUM('pending','accepted','declined','cancelled','expired') NOT NULL DEFAULT 'pending',
    game_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    CONSTRAINT fk_challenges_challenger FOREIGN KEY (challenger_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_challenges_challenged FOREIGN KEY (challenged_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_challenges_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table for matchmaking queue
CREATE TABLE IF NOT EXISTS matchmaking_queue (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    time_control ENUM('bullet','blitz','rapid','classical') NOT NULL DEFAULT 'blitz',
    rating_min INT UNSIGNED NOT NULL,
    rating_max INT UNSIGNED NOT NULL,
    status ENUM('waiting','matched','cancelled') NOT NULL DEFAULT 'waiting',
    game_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_matchmaking_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_matchmaking_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table for AI games
CREATE TABLE IF NOT EXISTS ai_games (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    game_id INT UNSIGNED NOT NULL,
    difficulty ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
    ai_engine VARCHAR(50) DEFAULT 'basic',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ai_games_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create a system AI user for AI games
INSERT INTO users (username, email, password_hash, role, rating) 
VALUES ('AI_Player', 'ai@system.local', '$2y$10$dummy', 'player', 1500)
ON DUPLICATE KEY UPDATE username=username;
