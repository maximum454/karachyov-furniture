<?php
use Bitrix\Main\Page\Asset;

// Нумерация строк ТОЛЬКО для множественных свойств в админке Битрикса
AddEventHandler("main", "OnEpilog", "AddNumbersToAdminPropertiesPage");

function AddNumbersToAdminPropertiesPage() {
    if (defined('ADMIN_SECTION') && ADMIN_SECTION === true) {
        Asset::getInstance()->addString('
        <style>
            .btx-row-num {
                display: inline-block !important;
                min-width: 22px;
                height: 22px;
                line-height: 22px;
                background: #2b78c5; /* Приятный синий цвет */
                color: #ffffff;
                font-weight: bold;
                text-align: center;
                border-radius: 4px;
                margin-right: 8px;
                font-size: 12px;
                vertical-align: middle;
            }
        </style>
        <script>
            BX.ready(function() {
                function applyRowNumbers() {
                    var mainTable = document.getElementById("edit1_edit_table");
                    if (!mainTable) return;

                    // Находим все строки свойств
                    var propertyRows = mainTable.querySelectorAll("tr[id^=\'tr_PROPERTY_\']");
                    
                    propertyRows.forEach(function(tr) {
                        var inputs = tr.querySelectorAll("select, input[type=\'text\']");
                        
                        // Игнорируем системные поля
                        var filteredInputs = Array.from(inputs).filter(function(input) {
                            return !input.id.includes("find_") && input.type !== "hidden";
                        });

                        // ПРОВЕРКА: Проставляем нумерацию ТОЛЬКО если элементов больше 1 (множественное свойство)
                        if (filteredInputs.length > 1) {
                            var count = 1;
                            filteredInputs.forEach(function(input) {
                                var parent = input.parentElement;
                                if (parent) {
                                    var existingBadge = parent.querySelector(".btx-row-num");
                                    if (!existingBadge) {
                                        var badge = document.createElement("span");
                                        badge.className = "btx-row-num";
                                        badge.innerText = count;
                                        parent.insertBefore(badge, input);
                                    } else {
                                        existingBadge.innerText = count;
                                    }
                                    count++;
                                }
                            });
                        } else {
                            // Если свойство не множественное, удаляем бейджи, если они успели появиться
                            tr.querySelectorAll(".btx-row-num").forEach(function(badge) {
                                badge.remove();
                            });
                        }
                    });
                }

                // Запуск при загрузке страницы
                setTimeout(applyRowNumbers, 300);

                // Запуск при кликах по кнопкам "Добавить" / "Еще"
                document.addEventListener("click", function(e) {
                    setTimeout(applyRowNumbers, 300);
                });
            });
        </script>
        ');
    }
}