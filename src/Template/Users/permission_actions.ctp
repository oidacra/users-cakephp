<ul>
<?php
foreach ($loadedPlugins as $loadedPlugin) {
    echo "<li>Path:" . \Cake\Core\Plugin::path($loadedPlugin) . '</li>';
}
?>
</ul>
