    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h3>Criminal Minds</h3>
                <p>Uw bron voor de laatste misdaadverslagen en onderzoeken.</p>
            </div>
            <div class="footer-links">
                <div class="footer-section">
                    <h4>Navigatie</h4>
                    <ul>
                        <li><a href="<?php echo $baseUrl; ?>/">Home</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?page=admin">Admin</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?page=search">Zoeken</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?page=disclaimer">Disclaimer & Veiligheid</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Disclaimer</h4>
                    <p>Lees onze <a href="<?php echo $baseUrl; ?>/?page=disclaimer">veiligheidsregels en disclaimer</a>.</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Criminal Minds. Alle rechten voorbehouden.</p>
        </div>
    </footer>

    <script src="<?php echo $baseUrl; ?>/js/main.js"></script>
</body>
</html>