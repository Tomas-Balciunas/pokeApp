<div class="navContainer">
    <?php if (isset($_SESSION['user_id'])): ?>
    <a href="/sonaro">Home</a>
    <a href="/sonaro/profile">Profile</a>
    <a href="/sonaro/logout">Log out</a>
    <?php else: ?>
        <h4>Welcome to the poke app, please log in to continue</h4>
    <?php endif; ?>
</div>