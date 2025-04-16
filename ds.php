<?php
/**
 * ============================================================================
 *  PHP Discord server status
 * ============================================================================
 *
 *  PHP Discord server status
 *  Simple discord server website status with automatic refresh.
 *
 *  @package       discordserverstatus
 *  @author        Yamiru <yamiru@yamiru.com>
 *  @copyright     Copyright (c) 2025, Yamiru.com
 *  @license       MIT
 *  @link          https://yamiru.com
 *  @repository    https://github.com/Yamiru/discordserverstatus
 *  
 * ============================================================================
 */


declare(strict_types=1);

// Configurable refresh interval (in seconds)
$refreshInterval = 110; // Set this value to your desired refresh time (in seconds)

// Define the Discord invite code directly in the PHP file
$inviteCode = 'YOUR_DISCORD_INVITE_CODE'; // Replace this with the actual invite code (without https://discord.gg/) PLEASE USE PERMANENT INVITE

// Function to fetch Discord server status using the invite code
function fetchDiscordServerStatus(string $inviteCode): string {
    $url = "https://discord.com/api/v10/invites/{$inviteCode}?with_counts=true";
    
    // Initialize cURL session
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Execute the request
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Decode the JSON response
    $data = json_decode($response, true);
    
    // Check if there's an error in the response
    if (isset($data['message'])) {
        return "<div class='error-message'>Error: " . htmlspecialchars($data['message']) . "</div>";
    }

    // Extract necessary details
    $serverName = htmlspecialchars($data['guild']['name']);
    $playersOnline = $data['approximate_presence_count'];
    $maxPlayers = $data['approximate_member_count'];

    // Format the player count as "current/maximum"
    $formattedPlayerCount = "{$playersOnline}/{$maxPlayers}";
    
    // Create the styled HTML output
    return <<<HTML
    <div class="server-status">
        <h1 class="server-name">{$serverName}</h1>
        <p class="player-count">Players: <strong>{$formattedPlayerCount}</strong></p>
    </div>
HTML;
}

// Fetch and display server status
echo fetchDiscordServerStatus($inviteCode);

?>

<!-- HTML & CSS for Styling -->
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #1e2a47; /* background */
        color: #ffffff; /* White text color */
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
        flex-direction: column;
    }

    .server-status {
        background-color: #2c3e50; /* for the server box */
        color: white;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        width: 300px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .server-name {
        font-size: 22px;
        margin-bottom: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #1692d2; /* text color */
    }

    .player-count {
        font-size: 18px;
        margin: 0;
    }

    .player-count strong {
        font-size: 22px;
        color: 	#b22b55; /* red color for player count */
    }

    .error-message {
        background-color: #e74c3c;
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin-top: 20px;
        font-weight: bold;
    }

    .refresh-message {
        margin-top: 20px;
        font-size: 16px;
        color: #ecf0f1;
    }
</style>

<!-- JavaScript to handle page refresh -->
<script>
    // Set the refresh interval in seconds (same as the PHP variable)
    const refreshInterval = <?php echo $refreshInterval; ?>;
    
    // Refresh the page after the specified interval
    setTimeout(function() {
        window.location.reload();
    }, refreshInterval * 1000);
</script>

<!-- Display a message indicating the page will refresh -->
<div class="refresh-message">
    <p>The page will automatically refresh in <span id="countdown"><?php echo $refreshInterval; ?></span> seconds.</p>
</div>

<script>
    // Countdown for page refresh
    let countdown = document.getElementById("countdown");
    let seconds = <?php echo $refreshInterval; ?>;
    let countdownInterval = setInterval(function() {
        seconds--;
        countdown.innerHTML = seconds;
        if (seconds <= 0) {
            clearInterval(countdownInterval);
        }
    }, 1000);
</script>
