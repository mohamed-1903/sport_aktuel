<?php
// model/watchlistModel.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function addToWatchlist(array $item): void {
    if (!isset($_SESSION['watchlist'])) {
        $_SESSION['watchlist'] = [];
    }

    foreach ($_SESSION['watchlist'] as $existing) {
        if ($existing['id'] === $item['id']) {
            return; // bereits vorhanden
        }
    }

    $_SESSION['watchlist'][] = $item;
}

function getWatchlistItems(): array {
    return $_SESSION['watchlist'] ?? [];
}

function removeFromWatchlist(int $id): void {
    if (!isset($_SESSION['watchlist'])) return;
    $_SESSION['watchlist'] = array_filter($_SESSION['watchlist'], function ($item) use ($id) {
        return $item['id'] !== $id;
    });
}

function clearWatchlist(): void {
    $_SESSION['watchlist'] = [];
}

function setWatchlistItems(array $items): void {
    $_SESSION['watchlist'] = $items;
}
