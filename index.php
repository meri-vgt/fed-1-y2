<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Moderne Blog</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --text-color: #1e293b;
            --text-light: #64748b;
            --bg-color: #ffffff;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        }

        .header-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
        }

        /* Navigation */
        nav {
            background: var(--bg-color);
            padding: 1rem 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-content {
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color);
            background-color: var(--bg-light);
            transform: translateY(-2px);
        }

        /* Main Content */
        main {
            padding: 4rem 0;
            background: var(--bg-light);
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .blog-card {
            background: var(--bg-color);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .blog-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            position: relative;
            overflow: hidden;
        }

        .blog-image::before {
            content: '📝';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            opacity: 0.7;
        }

        .blog-content {
            padding: 1.5rem;
        }

        .blog-date {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .blog-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-color);
            line-height: 1.3;
        }

        .blog-excerpt {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .read-more {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .read-more:hover {
            gap: 1rem;
        }

        .read-more::after {
            content: '→';
            transition: transform 0.3s ease;
        }

        .read-more:hover::after {
            transform: translateX(3px);
        }

        /* Featured Section */
        .featured {
            background: var(--bg-color);
            padding: 3rem 0;
            margin: 3rem 0;
            border-radius: 1rem;
            box-shadow: var(--shadow);
        }

        .featured-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .featured h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .featured p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Footer */
        footer {
            background: var(--text-color);
            color: white;
            padding: 3rem 0 2rem;
            text-align: center;
        }

        .footer-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .footer-link {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .footer-link:hover {
            opacity: 1;
        }

        .copyright {
            opacity: 0.6;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            .nav-content {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .blog-grid {
                grid-template-columns: 1fr;
            }

            .featured h2 {
                font-size: 2rem;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .blog-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .blog-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .blog-card:nth-child(3) {
            animation-delay: 0.2s;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>Mijn Moderne Blog</h1>
                <p class="subtitle">Ontdek inspirerende verhalen en verse inzichten</p>
            </div>
        </div>
    </header>

    <nav>
        <div class="container">
            <div class="nav-content">
                <a href="#home" class="nav-link">Home</a>
                <a href="#over" class="nav-link">Over Mij</a>
                <a href="#artikelen" class="nav-link">Artikelen</a>
                <a href="#contact" class="nav-link">Contact</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="blog-grid">
                <article class="blog-card">
                    <div class="blog-image"></div>
                    <div class="blog-content">
                        <div class="blog-date">8 september 2025</div>
                        <h2 class="blog-title">De Toekomst van Webontwikkeling</h2>
                        <p class="blog-excerpt">
                            Ontdek de nieuwste trends en technologieën die de webontwikkeling vormgeven. 
                            Van AI-gestuurde tools tot moderne frameworks, we verkennen wat er komt.
                        </p>
                        <a href="#" class="read-more">Lees verder</a>
                    </div>
                </article>

                <article class="blog-card">
                    <div class="blog-image"></div>
                    <div class="blog-content">
                        <div class="blog-date">5 september 2025</div>
                        <h2 class="blog-title">Moderne CSS Technieken</h2>
                        <p class="blog-excerpt">
                            Leer hoe je krachtige, moderne websites bouwt met de nieuwste CSS-functies. 
                            Van Grid Layout tot Custom Properties en meer.
                        </p>
                        <a href="#" class="read-more">Lees verder</a>
                    </div>
                </article>

                <article class="blog-card">
                    <div class="blog-image"></div>
                    <div class="blog-content">
                        <div class="blog-date">2 september 2025</div>
                        <h2 class="blog-title">Responsive Design Principes</h2>
                        <p class="blog-excerpt">
                            Begrijp de kernprincipes van responsive design en hoe je websites maakt 
                            die perfect werken op alle apparaten.
                        </p>
                        <a href="#" class="read-more">Lees verder</a>
                    </div>
                </article>
            </div>

            <section class="featured">
                <div class="container">
                    <div class="featured-content">
                        <h2>Blijf op de Hoogte</h2>
                        <p>
                            Mis geen enkel artikel! Abonneer je op mijn nieuwsbrief en ontvang 
                            wekelijks de nieuwste inzichten over webontwikkeling en design.
                        </p>
                        <a href="#" class="cta-button">Abonneer Nu</a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="#" class="footer-link">Privacy Beleid</a>
                    <a href="#" class="footer-link">Algemene Voorwaarden</a>
                    <a href="#" class="footer-link">Contact</a>
                    <a href="#" class="footer-link">Social Media</a>
                </div>
                <div class="copyright">
                    © <?php echo date('Y'); ?> Mijn Moderne Blog. Alle rechten voorbehouden.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>