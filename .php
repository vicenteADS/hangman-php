<?php

/**
 * Lista de palavras do jogo.
 * v1.0.0 — sem banco de dados, palavras hardcoded.
 */

function getRandomWord(): array
{
    $words = [
        ['word' => 'ELEFANTE',   'hint' => 'Animal com tromba'],
        ['word' => 'GUITARRA',   'hint' => 'Instrumento de cordas'],
        ['word' => 'MONTANHA',   'hint' => 'Elevação natural do terreno'],
        ['word' => 'BORBOLETA',  'hint' => 'Inseto com asas coloridas'],
        ['word' => 'CHOCOLATE',  'hint' => 'Doce feito de cacau'],
        ['word' => 'PIRAMIDE',   'hint' => 'Construção egípcia famosa'],
        ['word' => 'TARTARUGA',  'hint' => 'Réptil de casco'],
        ['word' => 'BIBLIOTECA', 'hint' => 'Lugar cheio de livros'],
        ['word' => 'COMPUTADOR', 'hint' => 'Máquina de processar dados'],
        ['word' => 'DINOSSAURO', 'hint' => 'Animal pré-histórico extinto'],
        ['word' => 'FOTOGRAFIA', 'hint' => 'Registro de imagem'],
        ['word' => 'HELICOPTERO','hint' => 'Aeronave com hélice'],
        ['word' => 'SUBMARINO',  'hint' => 'Veículo que navega submerso'],
        ['word' => 'UNIVERSO',   'hint' => 'Tudo que existe'],
        ['word' => 'VOCABULARIO','hint' => 'Conjunto de palavras de uma língua'],
    ];

    return $words[array_rand($words)];
}
