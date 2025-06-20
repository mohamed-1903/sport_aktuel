<?php
// model/cartModel.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function addToCart(array $item): void {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    foreach ($_SESSION['cart'] as &$existing) {
        if ($existing['id'] === $item['id'] && $existing['size'] === $item['size']) {
            $existing['quantity'] += $item['quantity'];
            return;
        }
    }

    $_SESSION['cart'][] = $item;
}

function getCartItems(): array {
    return $_SESSION['cart'] ?? [];
}

function removeFromCart(int $id, string $size): void {
    if (!isset($_SESSION['cart'])) return;
    $_SESSION['cart'] = array_values(array_filter(
        $_SESSION['cart'],
        function ($item) use ($id, $size) {
            return !($item['id'] === $id && $item['size'] === $size);
        }
    ));
}

// Funktion clearCart wird hier nicht mehr definiert, um Konflikte zu vermeiden

function updateCartQuantity(int $id, string $size, int $quantity): void {
    if (!isset($_SESSION['cart'])) return;

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $id && $item['size'] === $size) {
            $item['quantity'] = $quantity;
            break;
        }
    }
}
