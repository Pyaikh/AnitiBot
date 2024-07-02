<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Редактор исключений IP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Управление исключениями IP</h2>

<?php
$blacklist_file = rtrim($_SERVER['DOCUMENT_ROOT'], "/ ") . "/blacklist.json";
$whitelist_file = rtrim($_SERVER['DOCUMENT_ROOT'], "/ ") . "/whitelist.json";

function load_ips($file) {
    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        return json_decode($json_data, true);
    } else {
        return [];
    }
}

function save_ips($file, $ips) {
    $json_data = json_encode($ips, JSON_PRETTY_PRINT);
    file_put_contents($file, $json_data);
}

$blacklist = load_ips($blacklist_file);
$whitelist = load_ips($whitelist_file);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ip'])) {
    $new_ip = trim($_POST['new_ip']);
    if (filter_var($new_ip, FILTER_VALIDATE_IP)) {
        if (isset($_POST['blacklist'])) {
            if (!in_array($new_ip, $blacklist)) {
                $blacklist[] = $new_ip;
                save_ips($blacklist_file, $blacklist);
                echo "<p>IP-адрес $new_ip добавлен в черный список.</p>";
            } else {
                echo "<p>IP-адрес $new_ip уже есть в черном списке.</p>";
            }
        } else {
            if (!in_array($new_ip, $whitelist)) {
                $whitelist[] = $new_ip;
                save_ips($whitelist_file, $whitelist);
                echo "<p>IP-адрес $new_ip добавлен в белый список.</p>";
            } else {
                echo "<p>IP-адрес $new_ip уже есть в белом списке.</p>";
            }
        }
    } else {
        echo "<p>Некорректный IP-адрес.</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_ip'])) {
    $remove_ip = $_POST['remove_ip'];
    if (($key = array_search($remove_ip, $blacklist)) !== false) {
        unset($blacklist[$key]);
        save_ips($blacklist_file, $blacklist);
        echo "<p>IP-адрес $remove_ip удален из черного списка.</p>";
    } elseif (($key = array_search($remove_ip, $whitelist)) !== false) {
        unset($whitelist[$key]);
        save_ips($whitelist_file, $whitelist);
        echo "<p>IP-адрес $remove_ip удален из белого списка.</p>";
    } else {
        echo "<p>IP-адрес $remove_ip не найден в списках.</p>";
    }
}

?>

<form method="POST">
    <label for="new_ip">Добавить IP-адрес:</label>
    <input type="text" name="new_ip" id="new_ip" required>
    <label><input type="checkbox" name="blacklist"> Добавить в черный список</label>
    <button type="submit" name="add_ip">Добавить IP</button>
</form>

<h3>Текущие IP в черном списке</h3>
<ul>
    <?php foreach ($blacklist as $ip): ?>
        <li>
            <?php echo htmlspecialchars($ip); ?>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="remove_ip" value="<?php echo htmlspecialchars($ip); ?>">
                <button type="submit">Удалить</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

<h3>Текущие IP в белом списке</h3>
<ul>
    <?php foreach ($whitelist as $ip): ?>
        <li>
            <?php echo htmlspecialchars($ip); ?>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="remove_ip" value="<?php echo htmlspecialchars($ip); ?>">
                <button type="submit">Удалить</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

</body>
</html>
