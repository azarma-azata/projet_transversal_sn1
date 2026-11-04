<?php
/*discolure : AI was useed for math purpose only sadly it added a sh... a lot of comments
as you can see by the git history. I've taken the time to rewrite them or delete as see fit.
*/
require_once 'config.php';

const GAME_POINTS_PER_LIFE   = 10;
const GAME_POINTS_PER_LETTER = 5;

// Score multiplier: harder difficulty gives more points, even on short words.
function game_difficulty_multiplier(string $difficulty): float
{
    return match ($difficulty) {
        'hard'   => 3.0,
        'medium' => 1.5,
        default  => 1.0,
    };
}

// Single scoring formula, used by the selection cards AND game_save().
function game_points(string $difficulty, int $lives_left, int $word_len): int
{
    $base = max(0, $lives_left) * GAME_POINTS_PER_LIFE + $word_len * GAME_POINTS_PER_LETTER;
    return (int) round($base * game_difficulty_multiplier($difficulty));
}

// get constellation list by difficulty (easiest to harded)
function game_constellations(PDO $dbh): array
{
    return $dbh->query('SELECT `id`, `name`, `star_count`, `svg_path` FROM `constellation` ORDER BY `star_count` DESC, `id`')->fetchAll();
}

//sort the constellation into the different difficulty (UI only)
function game_difficulty_for(int $constellation_id, array $constellations): string
{
    $tiers = ['easy', 'medium', 'hard'];
    $last  = count($constellations) - 1;
    foreach ($constellations as $rank => $c) {
        if ((int) $c['id'] === $constellation_id) {
            return $last === 0 ? 'easy' : $tiers[(int) round($rank / $last * 2)];
        }
    }
    return 'easy';
}

//Generate the card information displayed at the constellation selection (word list, possible point gain)
function game_constellation_cards(PDO $dbh): array
{
    $constellations = game_constellations($dbh);
    $stats = $dbh->query('SELECT `difficulty`, COUNT(*) AS `n`, MIN(LENGTH(`word`)) AS `min_len`, MAX(LENGTH(`word`)) AS `max_len` FROM `words` GROUP BY `difficulty`')->fetchAll();
    $stats = array_column($stats, null, 'difficulty');

    $cards = [];
    foreach ($constellations as $c) {
        $difficulty = game_difficulty_for((int) $c['id'], $constellations);
        $s = $stats[$difficulty] ?? null;
        $c['difficulty'] = $difficulty;
        $c['word_count'] = $s ? (int) $s['n'] : 0;
        $c['points_min'] = $s ? game_points($difficulty, 1, (int) $s['min_len']) : 0;
        $c['points_max'] = $s ? game_points($difficulty, (int) $c['star_count'], (int) $s['max_len']) : 0;
        $cards[] = $c;
    }
    return $cards;
}

// Starts a new game. Returns an error message, or null on success.
function game_start(PDO $dbh, int $constellation_id): ?string
{
    $constellations = game_constellations($dbh);
    $chosen = null;
    foreach ($constellations as $c) {
        if ((int) $c['id'] === $constellation_id) {
            $chosen = $c;
        }
    }
    if ($chosen === null) {
        return 'Unknown constellation.';
    }

    $difficulty = game_difficulty_for($constellation_id, $constellations);

    $sth = $dbh->prepare('SELECT `id`, `word` FROM `words` WHERE `difficulty` = :difficulty ORDER BY RAND() LIMIT 1');
    $sth->execute(['difficulty' => $difficulty]);
    $word = $sth->fetch();
    if (!$word) {
        return 'No word available for this difficulty yet.';
    }

    $_SESSION['game'] = [
        'word_id'          => (int) $word['id'],
        'word'             => strtoupper($word['word']),
        'difficulty'       => $difficulty,
        'constellation_id' => (int) $chosen['id'],
        'name'             => $chosen['name'],
        'svg_path'         => $chosen['svg_path'],
        'star_count'       => (int) $chosen['star_count'],
        'guessed'          => [],
        'wrong'            => 0,
        'saved'            => false,
    ];
    return null;
}

function game_status(array $g): string
{
    if ($g['wrong'] >= $g['star_count']) {
        return 'lost';
    }
    foreach (str_split($g['word']) as $letter) {
        if (!in_array($letter, $g['guessed'], true)) {
            return 'playing';
        }
    }
    return 'won';
}

// Plays one letter. Invalid letters, repeated letters and finished games are ignored.
function game_guess(PDO $dbh, string $letter): void
{
    if (!isset($_SESSION['game'])) {
        return;
    }
    $g = &$_SESSION['game'];
    $letter = strtoupper($letter);

    if (game_status($g) !== 'playing' || !preg_match('/^[A-Z]$/', $letter) || in_array($letter, $g['guessed'], true)) {
        return;
    }

    $g['guessed'][] = $letter;
    if (strpos($g['word'], $letter) === false) {
        $g['wrong']++;
    }

    if (game_status($g) !== 'playing') {
        game_save($dbh, $g);
    }
}

// Writes the finished game to `games` (logged-in players only, once per game).
function game_save(PDO $dbh, array &$g): void
{
    if ($g['saved'] || !isset($_SESSION['user_id'])) {
        return;
    }
    $victory = game_status($g) === 'won';
    $points  = $victory
        ? game_points($g['difficulty'] ?? 'easy', (int) $g['star_count'] - (int) $g['wrong'], strlen($g['word']))
        : 0;

    $sth = $dbh->prepare('INSERT INTO `games` (`user_id`, `word_id`, `attempts`, `victory`, `points`) VALUES (:user_id, :word_id, :attempts, :victory, :points)');
    $sth->execute([
        'user_id'  => $_SESSION['user_id'],
        'word_id'  => $g['word_id'],
        'attempts' => count($g['guessed']),
        'victory'  => (int) $victory,
        'points'   => $points,
    ]);
    $g['saved'] = true;
}

// What the page needs to draw the current game (null = no game running).
function game_view(): ?array
{
    if (!isset($_SESSION['game'])) {
        return null;
    }
    $g = $_SESSION['game'];
    $status = game_status($g);

    $letters = [];
    foreach (str_split($g['word']) as $letter) {
        $letters[] = in_array($letter, $g['guessed'], true) || $status === 'lost' ? $letter : '_';
    }

    return [
        'name'    => $g['name'],
        'svg_path' => $g['svg_path'],
        'lives'   => $g['star_count'] - $g['wrong'],
        'wrong'   => $g['wrong'],
        'guessed' => $g['guessed'],
        'letters' => $letters,
        'status'  => $status,
        'word'    => $status === 'playing' ? null : $g['word'],
    ];
}

// Prints the constellation inline; one star (and its lines) goes dim per miss.
function game_render_constellation(array $view): void
{
    $file   = realpath(__DIR__ . '/../' . $view['svg_path']);
    $assets = realpath(__DIR__ . '/../assets');
    if ($file === false || $assets === false || strpos($file, $assets . DIRECTORY_SEPARATOR) !== 0) {
        return;
    }

    $css = '';
    for ($i = 0; $i < $view['wrong']; $i++) {
        $css .= "#s$i{opacity:.15}line[data-a=\"$i\"],line[data-b=\"$i\"]{opacity:.1}";
    }
    echo '<div class="constellation"><style>' . $css . '</style>';
    readfile($file);
    echo '</div>';
}

// Form handling (post/redirect/get, same pattern as login.php).
$game_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'start':
                $game_error = game_start($dbh, (int) ($_POST['constellation_id'] ?? 0)) ?? '';
                break;
            case 'guess':
                game_guess($dbh, (string) ($_POST['letter'] ?? ''));
                break;
            case 'quit':
                unset($_SESSION['game']);
                break;
        }
    } catch (PDOException $e) {
        $game_error = 'Something went wrong with the database.';
    }

    if ($game_error === '') {
        header('Location: game.php');
        exit;
    }
}
?>