<?php
// liste_cmd.php

require 'connexion.php';

// Handle Add/Insert Order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $date = $_POST['date'];
    $numcl = $_POST['numcl'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Commandes (date, numcl) VALUES (?, ?)");
        $stmt->execute([$date, $numcl]);
        header("Location: liste_cmd.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding order: " . $e->getMessage());
    }
}

// Handle Edit/Update Order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $numC = $_POST['numC'];
    $date = $_POST['date'];
    $numcl = $_POST['numcl'];

    try {
        $stmt = $pdo->prepare("UPDATE Commandes SET date = ?, numcl = ? WHERE numC = ?");
        $stmt->execute([$date, $numcl, $numC]);
        header("Location: liste_cmd.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating order: " . $e->getMessage());
    }
}

// Handle Delete Order
if (isset($_GET['delete'])) {
    $numC = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Commandes WHERE numC = ?");
        $stmt->execute([$numC]);
        header("Location: liste_cmd.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting order: " . $e->getMessage());
    }
}

// Fetch All Orders with Client Names
try {
    $sql = "SELECT Commandes.numC, Commandes.date, Clients.nom 
            FROM Commandes 
            JOIN Clients ON Commandes.numcl = Clients.numcl";
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}

// Fetch All Clients for Dropdown
try {
    $stmt_clients = $pdo->query("SELECT numcl, nom FROM Clients");
    $clients = $stmt_clients->fetchAll();
} catch (PDOException $e) {
    die("Error fetching clients: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Commandes</title>
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
    <h1 style="text-align: center;">Gestion des Commandes</h1>

    <!-- Add/Edit Form -->
    <form method="POST">
        <?php if (isset($_GET['edit'])): 
            $numC = $_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM Commandes WHERE numC = ?");
            $stmt->execute([$numC]);
            $order = $stmt->fetch();
        ?>
            <h3 style="text-align: center;">Modifier Commande</h3>
            <input type="hidden" name="numC" value="<?= $order['numC'] ?>">
            <div>
                <label>Date:</label>
                <input type="date" name="date" value="<?= $order['date'] ?>" required>
            </div>
            <div>
                <label>Client:</label>
                <select name="numcl" required>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['numcl'] ?>" <?= $client['numcl'] == $order['numcl'] ? 'selected' : '' ?>>
                            <?= $client['nom'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="edit">Enregistrer</button>
                <button type="button" onclick="window.location.href='liste_cmd.php';">Annuler</button>
            </div>
        <?php else: ?>
            <h3 style="text-align: center;">Ajouter une Commande</h3>
            <div>
                <label>Date:</label>
                <input type="date" name="date" required>
            </div>
            <div>
                <label>Client:</label>
                <select name="numcl" required>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['numcl'] ?>"><?= $client['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="add">Ajouter</button>
            </div>
        <?php endif; ?>
    </form>

    <!-- Order List -->
    <table>
        <thead>
            <tr>
                <th>Numéro Commande</th>
                <th>Date</th>
                <th>Client</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order['numC'] ?></td>
                    <td><?= $order['date'] ?></td>
                    <td><?= $order['nom'] ?></td>
                    <td>
                        <a href="liste_cmd.php?edit=<?= $order['numC'] ?>">Modifier</a>
                        <a href="liste_cmd.php?delete=<?= $order['numC'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">Supprimer</a>
                        <a href="affiche_commande.php?numC=<?= $order['numC'] ?>">Détails</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>