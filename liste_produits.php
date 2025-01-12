<?php
// liste_produits.php

require 'connexion.php';

// Handle Add/Insert Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $libelle = $_POST['libelle'];
    $prix = $_POST['prix'];
    $codeF = $_POST['codeF'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Produits (libelle, prix, codeF) VALUES (?, ?, ?)");
        $stmt->execute([$libelle, $prix, $codeF]);
        header("Location: liste_produits.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding product: " . $e->getMessage());
    }
}

// Handle Edit/Update Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $numP = $_POST['numP'];
    $libelle = $_POST['libelle'];
    $prix = $_POST['prix'];
    $codeF = $_POST['codeF'];

    try {
        $stmt = $pdo->prepare("UPDATE Produits SET libelle = ?, prix = ?, codeF = ? WHERE numP = ?");
        $stmt->execute([$libelle, $prix, $codeF, $numP]);
        header("Location: liste_produits.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating product: " . $e->getMessage());
    }
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $numP = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Produits WHERE numP = ?");
        $stmt->execute([$numP]);
        header("Location: liste_produits.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting product: " . $e->getMessage());
    }
}

// Fetch All Products
try {
    $stmt = $pdo->query("SELECT * FROM Produits");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Produits</title>
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
    <h1 style="text-align: center;">Gestion des Produits</h1>

    <!-- Add/Edit Form -->
    <form method="POST">
        <?php if (isset($_GET['edit'])): 
            $numP = $_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM Produits WHERE numP = ?");
            $stmt->execute([$numP]);
            $product = $stmt->fetch();
        ?>
            <h3 style="text-align: center;">Modifier Produit</h3>
            <input type="hidden" name="numP" value="<?= $product['numP'] ?>">
            <div>
                <label>Libellé:</label>
                <input type="text" name="libelle" value="<?= $product['libelle'] ?>" required>
            </div>
            <div>
                <label>Prix:</label>
                <input type="number" name="prix" value="<?= $product['prix'] ?>" step="0.01" required>
            </div>
            <div>
                <label>Code Fournisseur:</label>
                <input type="number" name="codeF" value="<?= $product['codeF'] ?>" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="edit">Enregistrer</button>
                <button type="button" onclick="window.location.href='liste_produits.php';">Annuler</button>
            </div>
        <?php else: ?>
            <h3 style="text-align: center;">Ajouter un Produit</h3>
            <div>
                <label>Libellé:</label>
                <input type="text" name="libelle" required>
            </div>
            <div>
                <label>Prix:</label>
                <input type="number" name="prix" step="0.01" required>
            </div>
            <div>
                <label>Code Fournisseur:</label>
                <input type="number" name="codeF" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="add">Ajouter</button>
            </div>
        <?php endif; ?>
    </form>

    <!-- Product List -->
    <table>
        <thead>
            <tr>
                <th>Numéro Produit</th>
                <th>Libellé</th>
                <th>Prix</th>
                <th>Code Fournisseur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['numP'] ?></td>
                    <td><?= $product['libelle'] ?></td>
                    <td><?= $product['prix'] ?></td>
                    <td><?= $product['codeF'] ?></td>
                    <td>
                        <a href="liste_produits.php?edit=<?= $product['numP'] ?>">Modifier</a>
                        <a href="liste_produits.php?delete=<?= $product['numP'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>