<?php
// affiche_commande.php

require 'connexion.php';

if (!isset($_GET['numC'])) {
    die("Order number not provided!");
}

$numC = $_GET['numC'];

// Handle Add/Insert Order Line
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $numP = $_POST['numP'];
    $qte = $_POST['qte'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Ligne_Commande (numC, numP, qte) VALUES (?, ?, ?)");
        $stmt->execute([$numC, $numP, $qte]);
        header("Location: affiche_commande.php?numC=$numC");
        exit();
    } catch (PDOException $e) {
        die("Error adding order line: " . $e->getMessage());
    }
}

// Handle Edit/Update Order Line
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $numP = $_POST['numP'];
    $qte = $_POST['qte'];

    try {
        $stmt = $pdo->prepare("UPDATE Ligne_Commande SET qte = ? WHERE numC = ? AND numP = ?");
        $stmt->execute([$qte, $numC, $numP]);
        header("Location: affiche_commande.php?numC=$numC");
        exit();
    } catch (PDOException $e) {
        die("Error updating order line: " . $e->getMessage());
    }
}

// Handle Delete Order Line
if (isset($_GET['delete'])) {
    $numP = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Ligne_Commande WHERE numC = ? AND numP = ?");
        $stmt->execute([$numC, $numP]);
        header("Location: affiche_commande.php?numC=$numC");
        exit();
    } catch (PDOException $e) {
        die("Error deleting order line: " . $e->getMessage());
    }
}

// Fetch Order Details
try {
    $sql_order = "SELECT Commandes.numC, Commandes.date, Clients.nom 
                  FROM Commandes 
                  JOIN Clients ON Commandes.numcl = Clients.numcl 
                  WHERE Commandes.numC = ?";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([$numC]);
    $order = $stmt_order->fetch();

    if (!$order) {
        die("Order not found!");
    }
} catch (PDOException $e) {
    die("Error fetching order details: " . $e->getMessage());
}

// Fetch Order Lines
try {
    $sql_lines = "SELECT Ligne_Commande.numP, Produits.libelle, Ligne_Commande.qte 
                  FROM Ligne_Commande 
                  JOIN Produits ON Ligne_Commande.numP = Produits.numP 
                  WHERE Ligne_Commande.numC = ?";
    $stmt_lines = $pdo->prepare($sql_lines);
    $stmt_lines->execute([$numC]);
    $order_lines = $stmt_lines->fetchAll();
} catch (PDOException $e) {
    die("Error fetching order lines: " . $e->getMessage());
}

// Fetch All Products for Dropdown
try {
    $stmt_products = $pdo->query("SELECT numP, libelle FROM Produits");
    $products = $stmt_products->fetchAll();
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Détails de la Commande</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        table, th, td { border: 1px solid black; text-align: center; }
        th, td { padding: 10px; }
        form { width: 50%; margin: 20px auto; }
        form div { margin-bottom: 15px; }
        button { padding: 10px 20px; }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Détails de la Commande</h1>

    <!-- Order Details -->
    <h2>Commande Numéro: <?= $order['numC'] ?></h2>
    <p>Date: <?= $order['date'] ?></p>
    <p>Client: <?= $order['nom'] ?></p>

    <!-- Add/Edit Order Line Form -->
    <form method="POST">
        <?php if (isset($_GET['edit_line'])): 
            $numP = $_GET['edit_line'];
            $stmt = $pdo->prepare("SELECT * FROM Ligne_Commande WHERE numC = ? AND numP = ?");
            $stmt->execute([$numC, $numP]);
            $line = $stmt->fetch();
        ?>
            <h3 style="text-align: center;">Modifier Ligne de Commande</h3>
            <input type="hidden" name="numP" value="<?= $line['numP'] ?>">
            <div>
                <label>Produit:</label>
                <select name="numP" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['numP'] ?>" <?= $product['numP'] == $line['numP'] ? 'selected' : '' ?>>
                            <?= $product['libelle'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Quantité:</label>
                <input type="number" name="qte" value="<?= $line['qte'] ?>" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="edit">Enregistrer</button>
                <button type="button" onclick="window.location.href='affiche_commande.php?numC=<?= $numC ?>';">Annuler</button>
            </div>
        <?php else: ?>
            <h3 style="text-align: center;">Ajouter une Ligne de Commande</h3>
            <div>
                <label>Produit:</label>
                <select name="numP" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['numP'] ?>"><?= $product['libelle'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Quantité:</label>
                <input type="number" name="qte" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="add">Ajouter</button>
            </div>
        <?php endif; ?>
    </form>

    <!-- Order Lines List -->
    <h3>Lignes de Commande:</h3>
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_lines as $line): ?>
                <tr>
                    <td><?= $line['libelle'] ?></td>
                    <td><?= $line['qte'] ?></td>
                    <td>
                        <a href="affiche_commande.php?numC=<?= $numC ?>&edit_line=<?= $line['numP'] ?>">Modifier</a>
                        <a href="affiche_commande.php?numC=<?= $numC ?>&delete=<?= $line['numP'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne de commande ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>