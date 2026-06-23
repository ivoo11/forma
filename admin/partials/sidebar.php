<aside class="admin-sidebar">

    <div class="admin-logo">
        FOЯMA CMS
    </div>

    <nav class="admin-nav">

        <a href="/admin/index.php">Dashboard</a>

        <a href="/admin/articles/index.php">
            Artículos
        </a>

        <a href="/admin/authors/index.php">
            Autores
        </a>

        <a href="/admin/categories/index.php">
            Categorías
        </a>

        <a href="/admin/questions/index.php">
            La Pregunta
        </a>

        <?php if (
            isset($_SESSION['user_role'])
            && $_SESSION['user_role'] === 'admin'
        ): ?>

            <a href="/admin/users/index.php">
                Usuarios
            </a>

        <?php endif; ?>

    </nav>

    <div class="admin-sidebar-footer">

        <div class="admin-user">

            <strong>
                <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
            </strong>

            <span>
                <?= htmlspecialchars(
                    ucfirst($_SESSION['user_role'] ?? '')
                ) ?>
            </span>

        </div>

        <a href="/admin/logout.php" class="admin-logout">
            Cerrar sesión
        </a>

    </div>

</aside>