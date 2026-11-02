<?php require 'backend/game.php'; $view = game_view(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendu</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/game.css">
</head>
<body>
    <div class="stars"></div>
    <?php require 'header.php'; ?>
    <main class="game<?= $view === null ? ' game--select' : '' ?>">
        <?php if ($game_error !== ''): ?>
            <p class="game-error" role="alert"><?= htmlspecialchars($game_error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($view === null): ?>
            <p class="game-intro">Pick a constellation. Each star is a life: the fewer stars, the harder the word.</p>
            <form method="post" class="game-choices">
                <input type="hidden" name="action" value="start">
                <?php foreach (game_constellation_cards($dbh) as $c): ?>
                    <div class="card">
                        <div class="card-stats">
                            <h2 class="card-name"><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <dl>
                                <div>
                                    <dt>Difficulty</dt>
                                    <dd class="difficulty difficulty--<?= $c['difficulty'] ?>"><?= ucfirst($c['difficulty']) ?></dd>
                                </div>
                                <div>
                                    <dt>Words in the set</dt>
                                    <dd><?= $c['word_count'] ?></dd>
                                </div>
                                <div>
                                    <dt>Points for a win</dt>
                                    <dd>
                                        <?php if ($c['word_count'] === 0): ?>
                                            -
                                        <?php elseif ($c['points_min'] === $c['points_max']): ?>
                                            +<?= $c['points_max'] ?>
                                        <?php else: ?>
                                            +<?= $c['points_min'] ?> to +<?= $c['points_max'] ?>
                                        <?php endif; ?>
                                    </dd>
                                </div>
                            </dl>
                            <button name="constellation_id" value="<?= (int) $c['id'] ?>" aria-label="Play <?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>">Play</button>
                        </div>
                        <?php game_render_constellation(['svg_path' => $c['svg_path'], 'wrong' => 0]); ?>
                    </div>
                <?php endforeach; ?>
            </form>
        <?php else: ?>
            <p class="game-intro"><?= htmlspecialchars($view['name'], ENT_QUOTES, 'UTF-8') ?> - <?= $view['lives'] ?> lives left</p>
            <?php game_render_constellation($view); ?>
            <p class="game-word"><?= htmlspecialchars(implode(' ', $view['letters']), ENT_QUOTES, 'UTF-8') ?></p>

            <?php if ($view['status'] === 'playing'): ?>
                <form method="post" class="game-keyboard">
                    <input type="hidden" name="action" value="guess">
                    <?php foreach (range('A', 'Z') as $letter): ?>
                        <button name="letter" value="<?= $letter ?>" <?= in_array($letter, $view['guessed'], true) ? 'disabled' : '' ?>><?= $letter ?></button>
                    <?php endforeach; ?>
                </form>
            <?php else: ?>
                <p class="game-result">
                    <?= $view['status'] === 'won' ? 'You saved the constellation!' : 'The constellation went dark. The word was ' . htmlspecialchars($view['word'], ENT_QUOTES, 'UTF-8') . '.' ?>
                </p>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="action" value="quit">
                <button class="game-new"><?= $view['status'] === 'playing' ? 'Give up' : 'New game' ?></button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>