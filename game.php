<?php

/**
 * game.php — Lógica central do Jogo da Forca.
 * v1.0.0
 */

require_once 'words.php';

const MAX_ERRORS = 6;

/**
 * Inicia uma nova partida, gravando o estado na sessão.
 */
function startGame(): void
{
    $entry = getRandomWord();

    $_SESSION['game'] = [
        'word'    => $entry['word'],
        'hint'    => $entry['hint'],
        'guesses' => [],
        'errors'  => 0,
        'status'  => 'playing', // playing | won | lost
    ];
}

/**
 * Processa a letra enviada pelo jogador.
 * Retorna uma mensagem de feedback.
 */
function guessLetter(string $rawLetter): string
{
    $letter = mb_strtoupper(trim($rawLetter), 'UTF-8');

    // Validação
    if (!preg_match('/^[A-ZÁÉÍÓÚÀÂÊÔÃÕÜÇ]$/u', $letter)) {
        return 'Envie apenas uma letra válida.';
    }

    $game = &$_SESSION['game'];

    if ($game['status'] !== 'playing') {
        return 'O jogo já terminou. Inicie uma nova partida.';
    }

    if (in_array($letter, $game['guesses'], true)) {
        return "Você já tentou a letra \"$letter\".";
    }

    $game['guesses'][] = $letter;

    if (mb_strpos($game['word'], $letter, 0, 'UTF-8') !== false) {
        $feedback = "Boa! A letra \"$letter\" está na palavra.";
    } else {
        $game['errors']++;
        $feedback = "Errou! A letra \"$letter\" não está na palavra.";
    }

    // Verifica fim de jogo
    $game['status'] = resolveStatus($game);

    return $feedback;
}

/**
 * Retorna 'won', 'lost' ou 'playing' com base no estado atual.
 */
function resolveStatus(array $game): string
{
    if ($game['errors'] >= MAX_ERRORS) {
        return 'lost';
    }

    // Verifica se todas as letras da palavra foram descobertas
    $letters = array_unique(mb_str_split($game['word'], 1, 'UTF-8'));
    foreach ($letters as $letter) {
        if (!in_array($letter, $game['guesses'], true)) {
            return 'playing';
        }
    }

    return 'won';
}

/**
 * Retorna a palavra mascarada: letras não descobertas viram '_'.
 */
function getMaskedWord(): string
{
    $game  = $_SESSION['game'];
    $chars = mb_str_split($game['word'], 1, 'UTF-8');

    return implode(' ', array_map(
        fn($c) => in_array($c, $game['guesses'], true) ? $c : '_',
        $chars
    ));
}

/**
 * Gera o SVG da forca com as partes do boneco conforme o número de erros.
 */
function renderGallows(int $errors): string
{
    // Partes do boneco: [visível a partir do erro N]
    $parts = [
        1 => '<circle cx="310" cy="90" r="20" stroke="currentColor" stroke-width="3" fill="none"/>',                  // cabeça
        2 => '<line x1="310" y1="110" x2="310" y2="180" stroke="currentColor" stroke-width="3"/>',                    // tronco
        3 => '<line x1="310" y1="130" x2="275" y2="160" stroke="currentColor" stroke-width="3"/>',                    // braço esq
        4 => '<line x1="310" y1="130" x2="345" y2="160" stroke="currentColor" stroke-width="3"/>',                    // braço dir
        5 => '<line x1="310" y1="180" x2="280" y2="220" stroke="currentColor" stroke-width="3"/>',                    // perna esq
        6 => '<line x1="310" y1="180" x2="340" y2="220" stroke="currentColor" stroke-width="3"/>',                    // perna dir
    ];

    $body = '';
    for ($i = 1; $i <= $errors; $i++) {
        $body .= $parts[$i] ?? '';
    }

    return <<<SVG
    <svg viewBox="0 0 400 280" xmlns="http://www.w3.org/2000/svg"
         class="gallows-svg" role="img" aria-label="Forca com {$errors} erro(s)">
      <!-- Estrutura da forca -->
      <line x1="50"  y1="260" x2="350" y2="260" stroke="currentColor" stroke-width="4"/>
      <line x1="150" y1="260" x2="150" y2="20"  stroke="currentColor" stroke-width="4"/>
      <line x1="150" y1="20"  x2="310" y2="20"  stroke="currentColor" stroke-width="4"/>
      <line x1="310" y1="20"  x2="310" y2="70"  stroke="currentColor" stroke-width="4"/>
      <!-- Boneco -->
      {$body}
    </svg>
    SVG;
}
