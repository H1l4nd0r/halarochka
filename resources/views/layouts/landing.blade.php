<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Финансовые решения - Главная</title>
    <style>
        /* Общие стили */
        body {
            padding-top: 56px;
        }
        
        /* Герой-секция */
        .hero-section {
            background: url('/images/abrafinbg.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 150px 0;
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hero-section p {
            font-size: 1.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Секция калькулятора */
        .calculator-section {
            padding: 50px 0;
            background-color: #f8f9fa;
            margin-bottom: 50px;
        }
        
        .calculation-results {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            height: 100%;
        }
        
        .result-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .result-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .result-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        
        /* Секция контактов */
        .contact-section {
            padding: 50px 0;
        }
        
        .map-container {
            height: 400px;
            background-color: #eee;
            margin-top: 30px;
        }
        
        /* Навигационная панель */
        .navbar {
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar.scrolled {
            background-color: rgba(0, 0, 0, 0.9) !important;
        }
        
        /* Адаптивные отступы */
        @media (max-width: 768px) {
            .hero-section {
                padding: 100px 0;
            }
            
            .hero-section h1 {
                font-size: 2rem;
            }
            
            .hero-section p {
                font-size: 1.2rem;
            }
        }
    </style>
    @vite(['resources/sass/app.scss','resources/js/app.js'])
</head>
<body>
    <!-- 1. Фиксированная навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://via.placeholder.com/40" width="40" height="40" class="d-inline-block align-top me-2">
                Abrafin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav ms-auto">
    <li class="nav-item">
        <a class="nav-link" href="#contacts">Контакты</a>
    </li>
</ul>
<a class="btn btn-primary ms-3" href="/login">Вход</a>
            </div>
        </div>
    </nav>

    <!-- 2. Герой-секция с фоновым изображением -->
    <section class="hero-section">
        <div class="container hero-content">
            <h1>Финансовые решения для вашего комфорта</h1>
            <p>Мы предлагаем выгодные условия кредитования и инвестирования. Наши специалисты помогут подобрать оптимальное решение для ваших целей.</p>
        </div>
    </section>

    <!-- 3. Калькулятор с полями и ползунками -->
    <section class="calculator-section" id="calculator">
        <div class="container">
            <h2 class="text-center mb-5">Калькулятор</h2>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="calculator-form">
                                <div class="mb-4">
                                    <label for="productPrice" class="form-label">Стоимость товара: <span id="productPrice-output">100 000</span> ₽</label>
                                    <input type="range" class="form-range" min="10000" max="1000000" step="1000" value="100000" id="productPrice" oninput="updateOutput('productPrice')">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="initialPayment" class="form-label">Первоначальный взнос: <span id="initialPayment-output">20 000</span> ₽</label>
                                    <input type="range" class="form-range" min="0" max="500000" step="1000" value="20000" id="initialPayment" oninput="updateOutput('initialPayment'); validateInitialPayment()">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="loanTerm" class="form-label">Срок в месяцах: <span id="loanTerm-output">6</span></label>
                                    <input type="range" class="form-range" min="1" max="12" value="6" id="loanTerm" oninput="updateOutput('loanTerm')">
                                </div>

                                <div class="mb-4">
                                    <label for="guarantors" class="form-label">Поручители:</label>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="guarantors" id="guarantor1" autocomplete="off" onclick="setGuarantors(1)" checked value="0.045" oninput="calculate();">
                                        <label class="btn btn-outline-primary" for="guarantor1">1</label>
                                        <input type="radio" class="btn-check" name="guarantors" id="guarantor2" autocomplete="off" onclick="setGuarantors(2)" value="0.04" oninput="calculate();">
                                        <label class="btn btn-outline-primary" for="guarantor2">2</label>
                                    </div>
                                </div>

                            </form>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="calculation-results">
                                <div class="result-item">
                                    <div class="result-label">Ежемесячный платеж:</div>
                                    <div class="result-value" id="monthlyPayment">0 ₽</div>
                                </div>
                                
                                <div class="result-item">
                                    <div class="result-label">Торговая наценка:</div>
                                    <div class="result-value" id="tradeMargin">0 ₽</div>
                                </div>
                                
                                <div class="result-item">
                                    <div class="result-label">Общая стоимость:</div>
                                    <div class="result-value" id="totalCost">0 ₽</div>
                                </div>
                                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#installmentModal">
                                    Оформить рассрочку
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Блок с контактами и картой -->
    <section class="contact-section bg-light">
        <div class="container">
            <h2 class="text-center mb-5" id="contacts">Наши контакты</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3>Офис компании</h3>
                    <address>
                        <strong>Финансовые решения</strong><br>
                        ул. Финансовая, д. 15, офис 304<br>
                        Москва, Россия, 123456<br>
                        <abbr title="Телефон">Тел:</abbr> +7 (495) 123-45-67<br>
                        <abbr title="Email">Email:</abbr> info@finance-solutions.ru
                    </address>
                    
                    <h4 class="mt-4">Часы работы</h4>
                    <p>
                        Понедельник - Пятница: 9:00 - 18:00<br>
                        Суббота - Воскресенье: выходной
                    </p>
                    
                    <h4 class="mt-4">Реквизиты</h4>
                    <p>
                        ИНН 1234567890<br>
                        ОГРН 1234567890123<br>
                        Банк: АО "Финансовый Банк"
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2245.3727899248037!2d37.617633315829034!3d55.75582668055398!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54a5a738fa419%3A0x7c347d506f52311f!2z0JrRgNCw0YHQvdCw0Y8g0J_Qu9C-0YnQsNC00Yw!5e0!3m2!1sru!2sru!4v1620000000000!5m2!1sru!2sru" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Футер -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Финансовые решения</h5>
                    <p>Мы помогаем реализовать ваши финансовые цели с 2010 года.</p>
                </div>
                <div class="col-md-3">
                    <h5>Меню</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Главная</a></li>
                        <li><a href="#" class="text-white">Кредиты</a></li>
                        <li><a href="#" class="text-white">Инвестиции</a></li>
                        <li><a href="#" class="text-white">Контакты</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Контакты</h5>
                    <address class="text-white">
                        +7 (495) 123-45-67<br>
                        info@finance-solutions.ru<br>
                        Москва, ул. Финансовая, 15
                    </address>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Финансовые решения. Все права защищены.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Функция для обновления значений при движении ползунков
        function updateOutput(sliderId) {
            const slider = document.getElementById(sliderId);
            const output = document.getElementById(sliderId + '-output');
            output.textContent = formatNumber(slider.value);
            calculate();
        }

        // Функция для обновления ползунка при изменении значения в поле ввода
        function updateSlider(sliderId) {
            const slider = document.getElementById(sliderId);
            const input = document.getElementById(sliderId + '-output');
            slider.value = input.textContent.replace(/\s/g, '');
            calculate();
        }
        
        // Проверка, чтобы первоначальный взнос не превышал стоимость товара
        function validateInitialPayment() {
            const productPrice = parseInt(document.getElementById('productPrice').value);
            const initialPayment = parseInt(document.getElementById('initialPayment').value);
            
            if (initialPayment > productPrice) {
                document.getElementById('initialPayment').value = productPrice;
                document.getElementById('initialPayment-output').textContent = formatNumber(productPrice);
            }
            
            calculate();
        }
        
        // Форматирование чисел с разделителями
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }
        
// Функция для расчета рассрочки
function calculate() {
    const productPrice = parseInt(document.getElementById('productPrice').value);
    const initialPayment = parseInt(document.getElementById('initialPayment').value);
    const loanTerm = parseInt(document.getElementById('loanTerm').value);
    const monthlyRate = document.querySelector('input[name="guarantors"]:checked').value; // 4.5% или 4%
    //alert(monthlyRate);

    // Сумма кредита
    const loanAmount = productPrice - initialPayment;

    if (loanAmount <= 0) {
        // Если первоначальный взнос покрывает стоимость товара
        document.getElementById('monthlyPayment').textContent = "0 ₽";
        document.getElementById('tradeMargin').textContent = formatNumber(Math.round(productPrice * 0.1)) + " ₽";
        document.getElementById('totalCost').textContent = formatNumber(productPrice) + " ₽";
        document.getElementById('loanAmount').textContent = "0 ₽";
        document.getElementById('overpayment').textContent = "0 ₽";
        return;
    }

    // Торговая наценка (от стоимости товара и срока по ставке 4% в месяц)
    const tradeMargin = loanAmount * monthlyRate * loanTerm;

    // Ежемесячный платеж (рассрочка)
    const monthlyPayment = ( tradeMargin + loanAmount ) / loanTerm;

    // Общая сумма выплат
    const totalPayments = monthlyPayment * loanTerm;

    // Переплата
    const overpayment = totalPayments - loanAmount;


    // Общая стоимость (товар + наценка)
    const totalCost = productPrice + tradeMargin;

    // Обновляем значения на странице
    document.getElementById('monthlyPayment').textContent = formatNumber(Math.round(monthlyPayment)) + " ₽";
    document.getElementById('tradeMargin').textContent = formatNumber(Math.round(tradeMargin)) + " ₽";
    document.getElementById('totalCost').textContent = formatNumber(Math.round(totalCost)) + " ₽";
    document.getElementById('loanAmount').textContent = formatNumber(Math.round(loanAmount)) + " ₽";
    document.getElementById('overpayment').textContent = formatNumber(Math.round(overpayment)) + " ₽";
}
        
        // Изменение навбара при прокрутке
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Инициализация значений при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            updateOutput('productPrice');
            updateOutput('initialPayment');
            updateOutput('loanTerm');
            calculate();
        });
    </script>
<!-- Modal for Installment Form -->
<div class="modal fade" id="installmentModal" tabindex="-1" aria-labelledby="installmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="installmentModalLabel">Оформить рассрочку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="installmentForm">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Наименование товара</label>
                        <input type="text" class="form-control" id="productName" required>
                    </div>
                    <div class="mb-3">
                        <label for="storeName" class="form-label">Магазин</label>
                        <input type="text" class="form-control" id="storeName" required>
                    </div>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">ФИО</label>
                        <input type="text" class="form-control" id="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Телефон</label>
                        <input type="tel" class="form-control" id="phoneNumber" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить заявку</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Service Response -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Результат</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="responseModalBody">
                <!-- Service response will be displayed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ок</button>
            </div>
        </div>
    </div>
</div>

<script>

    document.getElementById('installmentForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = {
            productName: document.getElementById('productName').value,
            storeName: document.getElementById('storeName').value,
            fullName: document.getElementById('fullName').value,
            phoneNumber: document.getElementById('phoneNumber').value
        };

        // Call the service
        let result = 'Попробуйте отправить еще раз чуть позже.';
        fetch('/deals/apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            result = data.message;
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            // Close the installment modal
            const installmentModal = bootstrap.Modal.getInstance(document.getElementById('installmentModal'));
            installmentModal.hide();

            // Show the response modal
            const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));
            document.getElementById('responseModalBody').textContent = result;
            responseModal.show();
        });

    });
</script>
</body>
</html>
