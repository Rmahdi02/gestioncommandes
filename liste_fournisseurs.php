<?php
// liste_fournisseurs.php

require 'connexion.php';

// Handle Add/Insert Supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Fournisseurs (nom, adresse) VALUES (?, ?)");
        $stmt->execute([$nom, $adresse]);
        header("Location: liste_fournisseurs.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding supplier: " . $e->getMessage());
    }
}

// Handle Edit/Update Supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $codeF = $_POST['codeF'];
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];

    try {
        $stmt = $pdo->prepare("UPDATE Fournisseurs SET nom = ?, adresse = ? WHERE codeF = ?");
        $stmt->execute([$nom, $adresse, $codeF]);
        header("Location: liste_fournisseurs.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating supplier: " . $e->getMessage());
    }
}

// Handle Delete Supplier
if (isset($_GET['delete'])) {
    $codeF = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Fournisseurs WHERE codeF = ?");
        $stmt->execute([$codeF]);
        header("Location: liste_fournisseurs.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting supplier: " . $e->getMessage());
    }
}

// Fetch All Suppliers
try {
    $stmt = $pdo->query("SELECT * FROM Fournisseurs");
    $suppliers = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching suppliers: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Fournisseurs</title>
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
    <h1 style="text-align: center;">Gestion des Fournisseurs</h1>

    <!-- Add/Edit Form -->
    <form method="POST">
        <?php if (isset($_GET['edit'])): 
            $codeF = $_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM Fournisseurs WHERE codeF = ?");
            $stmt->execute([$codeF]);
            $supplier = $stmt->fetch();
        ?>
            <h3 style="text-align: center;">Modifier Fournisseur</h3>
            <input type="hidden" name="codeF" value="<?= $supplier['codeF'] ?>">
            <div>
                <label>Nom:</label>
                <input type="text" name="nom" value="<?= $supplier['nom'] ?>" required>
            </div>
            <div>
                <label>Adresse:</label>
                <input type="text" name="adresse" value="<?= $supplier['adresse'] ?>" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="edit">Enregistrer</button>
                <button type="button" onclick="window.location.href='liste_fournisseurs.php';">Annuler</button>
            </div>
        <?php else: ?>
            <h3 style="text-align: center;">Ajouter un Fournisseur</h3>
            <div>
                <label>Nom:</label>
                <input type="text" name="nom" required>
            </div>
            <div>
                <label>Adresse:</label>
                <input type="text" name="adresse" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="add">Ajouter</button>
            </div>
        <?php endif; ?>
    </form>

    <!-- Supplier List -->
    <table>
        <thead>
            <tr>
                <th>Code Fournisseur</th>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?= $supplier['codeF'] ?></td>
                    <td><?= $supplier['nom'] ?></td>
                    <td><?= $supplier['adresse'] ?></td>
                    <td>
                        <a href="liste_fournisseurs.php?edit=<?= $supplier['codeF'] ?>">Modifier</a>
                        <a href="liste_fournisseurs.php?delete=<?= $supplier['codeF'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>