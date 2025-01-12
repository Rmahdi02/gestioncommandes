<?php
// liste_clients.php

require 'connexion.php';

// Handle Add/Insert Client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $tel = $_POST['tel'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Clients (nom, adresse, tel) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $adresse, $tel]);
        header("Location: liste_clients.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding client: " . $e->getMessage());
    }
}

// Handle Edit/Update Client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $numcl = $_POST['numcl'];
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $tel = $_POST['tel'];

    try {
        $stmt = $pdo->prepare("UPDATE Clients SET nom = ?, adresse = ?, tel = ? WHERE numcl = ?");
        $stmt->execute([$nom, $adresse, $tel, $numcl]);
        header("Location: liste_clients.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating client: " . $e->getMessage());
    }
}

// Handle Delete Client
if (isset($_GET['delete'])) {
    $numcl = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Clients WHERE numcl = ?");
        $stmt->execute([$numcl]);
        header("Location: liste_clients.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting client: " . $e->getMessage());
    }
}

// Fetch All Clients
try {
    $stmt = $pdo->query("SELECT * FROM Clients");
    $clients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching clients: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Clients</title>
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
    <h1 style="text-align: center;">Gestion des Clients</h1>

    <!-- Add/Edit Form -->
    <form method="POST">
        <?php if (isset($_GET['edit'])): 
            $numcl = $_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM Clients WHERE numcl = ?");
            $stmt->execute([$numcl]);
            $client = $stmt->fetch();
        ?>
            <h3 style="text-align: center;">Modifier Client</h3>
            <input type="hidden" name="numcl" value="<?= $client['numcl'] ?>">
            <div>
                <label>Nom:</label>
                <input type="text" name="nom" value="<?= $client['nom'] ?>" required>
            </div>
            <div>
                <label>Adresse:</label>
                <input type="text" name="adresse" value="<?= $client['adresse'] ?>" required>
            </div>
            <div>
                <label>Téléphone:</label>
                <input type="text" name="tel" value="<?= $client['tel'] ?>" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="edit">Enregistrer</button>
                <button type="button" onclick="window.location.href='liste_clients.php';">Annuler</button>
            </div>
        <?php else: ?>
            <h3 style="text-align: center;">Ajouter un Client</h3>
            <div>
                <label>Nom:</label>
                <input type="text" name="nom" required>
            </div>
            <div>
                <label>Adresse:</label>
                <input type="text" name="adresse" required>
            </div>
            <div>
                <label>Téléphone:</label>
                <input type="text" name="tel" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="add">Ajouter</button>
            </div>
        <?php endif; ?>
    </form>

    <!-- Client List -->
    <table>
        <thead>
            <tr>
                <th>Numéro Client</th>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= $client['numcl'] ?></td>
                    <td><?= $client['nom'] ?></td>
                    <td><?= $client['adresse'] ?></td>
                    <td><?= $client['tel'] ?></td>
                    <td>
                        <a href="liste_clients.php?edit=<?= $client['numcl'] ?>">Modifier</a>
                        <a href="liste_clients.php?delete=<?= $client['numcl'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>