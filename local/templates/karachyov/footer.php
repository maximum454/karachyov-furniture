<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
    </main>

    <!-- 7. FOOTER -->
    <footer class="footer" id="contacts">
        <div class="footer__container">
            <div class="footer__main">
                <!-- Блок бренда -->
                <div class="footer__brand">
                    <a href="/" class="footer__logo logo" aria-label="KARACHYOV - На главную">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/logo-B_s-j38y.png" alt="Логотип KARACHYOV" class="logo__img" width="212" height="212">
                    </a>
                </div>

                <!-- Колонки навигации -->
                <div class="footer__column">
                    <h3 class="footer__column-title">Каталог</h3>
                    <ul class="footer__list">
                        <li class="footer__item"><a href="#catalog" class="footer__link">Soft Arc</a></li>
                        <li class="footer__item"><a href="#catalog" class="footer__link">Soft Lines</a></li>
                    </ul>
                </div>

                <div class="footer__column">
                    <h3 class="footer__column-title">Покупателю</h3>
                    <ul class="footer__list">
                        <li class="footer__item"><a href="#about" class="footer__link">О нас</a></li>
                        <li class="footer__item"><a href="#" class="footer__link">Гарантия</a></li>
                        <li class="footer__item"><a href="#" class="footer__link">Доставка и сборка</a></li>
                        <li class="footer__item"><a href="#" class="footer__link">Оплата</a></li>
                        <li class="footer__item"><a href="#" class="footer__link">Возврат и обмен</a></li>
                        <li class="footer__item"><a href="#" class="footer__link">Частые вопросы</a></li>
                    </ul>
                </div>

                <div class="footer__column">
                    <h3 class="footer__column-title">Контакты</h3>
                    <address class="footer__contacts">
                        <a href="tel:+77477747047" class="footer__contact-link">+7 747 774 70 47</a>
                        <a href="mailto:info@karachyov.kz" class="footer__contact-link">info@karachyov.kz</a>
                        <p class="footer__address">г. Астана, ул. Брусиловского</p>
                        <p class="footer__worktime">Ежедневно с 10:00 до 18:00</p>
                    </address>
                </div>

                <div class="footer__column">
                    <h3 class="footer__column-title">Мы в соцсетях</h3>
                    <ul class="footer__socials socials">
                        <li class="socials__item">
                            <a href="https://instagram.com" class="socials__link" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAOdEVYdFNvZnR3YXJlAEZpZ21hnrGWYwAABORJREFUeAHlWs2PFUUQ/+0uaERRREUxgouJejGiCTEcPKCLHjyzu2iE+CfoQRM9iUHjmnjxoOjFoEaNZzzoATVeNKLGgxcOZPlI+IYs31+7j/rRVUy9pudtz7zZ2QO/5Jd5M9PVVTXTXVXd84CbDAOYe1TSMVChTced3yVcLFzortcF+7siPC08mbjXmU24FwaFM/r7EeFrwpeEjwlvEw6hGQemhWeEe4Q7hNv1d2xDUrjs+oAK3if8RDiqBhPHhYeEl9D/sOIDuEX1LHPXvhe+ITyM4EQHmQ/LjCdeEJ5VwSnhVuGTCMNnMGpfl9C+OCxXq46jqvOU2mBtsh6WGbYZhdefChehPSxRnaZ/c2RbKazBi054Q+L+XMLr2ODsWK/XhsoE7fXcj/DqvPH+dbcBr29UbeFQvsfdL8V3KvBZorO5wlDi3Ou14fQNCpuSYKhk5DmCMKl6Nm4IZvyw8D3h4+666eb8YxBhvliV6sQasgN6ulXP2xjzxAoUw/YCQp5BZMMHen+Lnicf7N/aaDXaxVuq9xK6jfR4Su/9BWe89+JuhOzHTh5FeCK58NnyduFa4RrhclXKpLdL+CdCxiU4RKb190PC/4RLVe8Twv2RDuaeSYTyhW9sKjZipfCy8H/kDx0/0Ri7P0KoZzolZIKaQDG/qGeB/n5Q+KbaYQ56DKltl12bLtgE/gfVjR8RnlAjzwt/Er4tfEX4svAd4c+qnG2OCZ9POOGNTYG2TautN2CVcyAn8qQy9naE4VAGvvqvUCPDKsyBZCSq4oApHHHGjLn7KXl/bczJjei10gzr8C8acoDgOD6uRow6I3vJ+vvjKss+7kQeGnVgAsWwAaolPGv7tfYxkSnXmAMMaYwonJQrIqNyYG0ZTZi4GAByqt0bHKibaZ8R3ivciRCvZ136ReiozD7h7wg5aC1qoK4Da/T4mzOoKkzm16jPSqjrwAN63Iv+ManH5aiBfou1fhf0ffdV14FDehxG/xiO+qyEug7s0uM6PdZZM5jMc1GftVEljLLiZNHGENhvGOUihQuoOzLkGgujXJ9+IbwVYaHhjaqC9xFKiC9RlNm1UTUTM/1b6Wx10Gx7N76U2IiiMm29lLDCyxdz4+g2NIa/ttHJrYv67IVGayFr48tp1jYre8g8jLCz0Fg5vSBhEJAXk60cYH1/QPiD8FWEBcwvykltS4WMNs8izBvWPqxgd6J6GRLbeh2MJrakzHmdBnt6HMesKm1fM0Ua/iFCMWiG5E7+QRRLSot8XcJc07K44qJ+GNWigl+gMxyy2LNFPXEQIc7/ITznDJpBPrj+2I2wk80lZfwt4ZozVDIf2yo5eBrBtq6E5ycPb/6ov0cT9+cLZoPZtEOPyaHHycbMyD2XRb0atgS/ZcO5VRpCfeNv0b25m/1hoWH4Cb4NGZu7dpErLfsqM5borA14fbaDwVGxzN1PwsLneqS3S9r+wDGOGtsv1sEmJ/w5wjhsC6x2tzn9myLbZoW9Ir4J2/bmJGL1yHC2BM1/5FusfbO6nULxVWYk0pM0NAVLNPzk9DHCPqe1Z/3OKvIimgHLC66zl+o5IyE/s76OsPFV6TOrh39lDF/vIuzPMwsypc80RPZ1Svvegu6las9hkxtZ4oKL+zgsGRaiGdAB/tXAhk6Z3r7RRiidz8TZPq4CMxZNKmwuGvkAAAAASUVORK5CYII=" alt="" aria-hidden="true" width="48" height="48">
                            </a>
                        </li>
                        <li class="socials__item">
                            <a href="https://wa.me/77477747047" class="socials__link" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                                <img src="data:image/png;base64,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" alt="" aria-hidden="true" width="48" height="48">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Нижняя плашка футера -->
        <div class="footer__after">
            <div class="footer__container">
                <div class="footer__bottom">
                    <p class="footer__copyright">© <?= date('Y') ?> KARACHYOV. Все права защищены.</p>
                    <div class="footer__legal">
                        <a href="/privacy-policy" class="footer__legal-link">Политика конфиденциальности</a>
                        <a href="/terms" class="footer__legal-link">Пользовательское соглашение</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>