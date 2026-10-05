<?php

$katalog = __DIR__ . DIRECTORY_SEPARATOR . 'dokumenty';
$komunikat = '';
$typKomunikatu = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nazwa'])) {
    $nazwa = trim($_POST['nazwa']);

    if ($nazwa === '') {
        $komunikat = 'Podaj nazwę katalogu.';
        $typKomunikatu = 'blad';
    } elseif (!preg_match('/^[\p{L}0-9_\-]+$/u', $nazwa)) {
        $komunikat = 'Nazwa może zawierać tylko litery, cyfry, myślnik i podkreślenie.';
        $typKomunikatu = 'blad';
    } else {
        $sciezka = $katalog . DIRECTORY_SEPARATOR . $nazwa;

        if (file_exists($sciezka)) {
            $komunikat = 'Element o nazwie „' . $nazwa . '” już istnieje.';
            $typKomunikatu = 'blad';
        } elseif (mkdir($sciezka)) {
            $komunikat = 'Utworzono katalog „' . $nazwa . '”.';
            $typKomunikatu = 'ok';
        } else {
            $komunikat = 'Nie udało się utworzyć katalogu.';
            $typKomunikatu = 'blad';
        }
    }
}


$elementy = [];
if (is_dir($katalog)) {
    foreach (scandir($katalog) as $element) {
        if ($element === '.' || $element === '..') {
            continue;
        }
        $pelna = $katalog . DIRECTORY_SEPARATOR . $element;
        if (is_file($pelna)) {
            $typ = 'PLIK';
        } elseif (is_dir($pelna)) {
            $typ = 'KATALOG';
        } else {
            $typ = 'INNE';
        }
        $elementy[] = ['nazwa' => $element, 'typ' => $typ];
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moje pliki</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>MOJE PLIKI</h1>
    </header>

    <main>
        <section>
            <h2>Aktualny katalog:</h2>
            <p class="sciezka"><?= htmlspecialchars(getcwd()) ?></p>
        </section>

        <section>
            <h2>ZAWARTOŚĆ KATALOGU</h2>

            <?php if (!is_dir($katalog)): ?>
                <p class="komunikat blad">Katalog „dokumenty” nie istnieje.</p>
            <?php elseif (count($elementy) === 0): ?>
                <p>Katalog jest pusty.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr><th>Nazwa</th><th>Typ</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($elementy as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['nazwa']) ?></td>
                                <td class="typ-<?= strtolower($e['typ']) ?>"><?= $e['typ'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section>
            <h2>Nowy katalog</h2>

            <?php if ($komunikat !== ''): ?>
                <p class="komunikat <?= $typKomunikatu ?>"><?= htmlspecialchars($komunikat) ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <label for="nazwa">Nazwa katalogu:</label>
                <input type="text" id="nazwa" name="nazwa" placeholder="np. materialy" required>
                <button type="submit">Utwórz</button>
            </form>
        </section>
    </main>
</body>
</html>
