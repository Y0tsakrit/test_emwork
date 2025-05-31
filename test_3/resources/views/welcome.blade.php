<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ระบบรายรับ-รายจ่าย</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-[80%] h-screen flex flex-col items-center gap-5 mx-auto">
    <div class="text-2xl font-bold mb-4">ระบบรายรับ-รายจ่าย</div>
    <div class="mb-4 flex flex-row gap-4 items-center">
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="showAddForm()">เพิ่มรายการ</button>
        <div class="flex items-center gap-2">
            <label for="month-filter" class="font-medium">เลือกเดือน:</label>
            <input type="month" id="month-filter" class="border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>
    <div class="flex justify-between flex-row gap-3">
        <div class="flex flex-col gap-2 items-center border-green-600 border-2 w-[300px] h-[150px] rounded-lg bg-green-600">
            <div class="text-white">รวมรายรับ</div>
            <div id="total-income" class="text-white">0 บาท</div>
        </div>
        <div class="flex flex-col gap-2 items-center border-red-600 border-2 w-[300px] h-[150px] rounded-lg bg-red-600">
            <div class="text-white">รวมรายจ่าย</div>
            <div id="total-expense" class="text-white">0 บาท</div>
        </div>
        <div class="flex flex-col gap-2 items-center border-green-600 border-2 w-[300px] h-[150px] rounded-lg bg-green-600">
            <div class="text-white">รวมรายรับ-จ่าย</div>
            <div id="total-balance" class="text-white">0 บาท</div>
        </div>
    </div>
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-300 px-4 py-2">ประเภท</th>
                <th class="border border-gray-300 px-4 py-2">ชื่อรายการ</th>
                <th class="border border-gray-300 px-4 py-2">จำนวนเงิน</th>
                <th class="border border-gray-300 px-4 py-2">วันที่จ่าย</th>
                <th class="border border-gray-300 px-4 py-2">บันทึกเมื่อ</th>
                <th class="border border-gray-300 px-4 py-2">แก้ไขเมื่อ</th>
                <th class="border border-gray-300 px-4 py-2"></th>
                <th class="border border-gray-300 px-4 py-2"></th>
            </tr>
        </thead>
        <tbody id="data-table" class="text-sm">
        </tbody>
    </table>

    <div id="modal" class="fixed inset-0 flex justify-center items-center bg-gray-900 z-50" style="background-color: rgba(0, 0, 0, 0.5); display: none;">
        <div class="bg-white w-[700px] h-[400px] rounded-xl p-6">
            <h3 class="text-xl font-bold mb-4">เพิ่มรายการใหม่</h3>
            <form id="account-form" class="flex flex-col gap-4">
                <select id="accType" name="accType" class="text-lg font-bold p-3 w-[150px] border border-gray-300 rounded-lg" required>
                    <option value="รายรับ">รายรับ</option>
                    <option value="รายจ่าย">รายจ่าย</option>
                </select>
                <input id="accName" name="accName" type="text" placeholder="ชื่อรายการ" class="border border-gray-300 rounded-lg px-3 py-2 w-full" required>
                <input id="accAmount" name="accAmount" type="number" step="0.01" placeholder="จำนวนเงิน" class="border border-gray-300 rounded-lg px-3 py-2 w-full" required>
                <input id="accDate" name="accDate" type="date" class="border border-gray-300 rounded-lg px-3 py-2 w-full" required>
                <div class="flex flex-row justify-end mt-7 gap-5">
                    <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" onclick="hideAddForm()">ยกเลิก</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

        <div id="edit-modal" class="fixed inset-0 flex justify-center items-center bg-gray-900 z-50" style="background-color: rgba(0, 0, 0, 0.5); display: none;">
        <div class="bg-white w-[700px] h-[400px] rounded-xl p-6">
            <h3 class="text-xl font-bold mb-4">แก้ไขรายการ</h3>
            <form id="edit-account-form" class="flex flex-col gap-4" onsubmit="handleEditSubmit(event)">
                <input id="edit-accId" name="id" type="hidden">
                <select id="edit-accType" name="accType" class="text-lg font-bold p-3 w-[150px] border border-gray-300 rounded-lg" required>
                    <option value="รายรับ">รายรับ</option>
                    <option value="รายจ่าย">รายจ่าย</option>
                </select>
                <input id="edit-accName" name="accName" type="text" placeholder="ชื่อรายการ" class="border border-gray-300 rounded-lg px-3 py-2 w-full" required>
                <input id="edit-accAmount" name="accAmount" type="number" step="0.01" placeholder="จำนวนเงิน" class="border border-gray-300 rounded-lg px-3 py-2 w-full" required>
                <input id="edit-accDate" name="accDate" type="date" class="border border-gray-300 rounded-lg px-3 py-2 w-full" required>
                <div class="flex flex-row justify-end mt-7 gap-5">
                    <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" onclick="hideEditForm()">ยกเลิก</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<script>
    // Set default month filter to the current month when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date();
        const year = today.getFullYear();
        // Month is 0-indexed, so add 1 and pad with '0' for single digits
        const month = String(today.getMonth() + 1).padStart(2, '0');
        // Set the value of the month input to YYYY-MM format
        document.getElementById('month-filter').value = `${year}-${month}`;
        fetchData(); // Fetch data for the current month immediately
    });

    // Add an event listener to the month filter to refetch data when it changes
    document.getElementById('month-filter').addEventListener('change', fetchData);

    async function fetchData() {
        try {
            // Get the selected month from the input (e.g., "2025-05")
            const selectedMonth = document.getElementById('month-filter').value;
            let apiUrl = '/api/accounts';

            // If a month is selected, append it as a query parameter
            if (selectedMonth) {
                apiUrl += `?month=${selectedMonth}`;
            }

            const response = await fetch(apiUrl);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Data received:', data);

            let income = 0;
            let expense = 0;
            const tableBody = document.getElementById('data-table');
            tableBody.innerHTML = ''; // Clear existing table rows

            data.forEach(account => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="border border-gray-300 px-4 py-2 text-center">${account.accType || ''}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">${account.accName || ''}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">${account.accAmount || 0}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">${account.accDate || ''}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">${account.created_at ? new Date(account.created_at).toLocaleString() : ''}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">${account.updated_at ? new Date(account.updated_at).toLocaleString() : ''}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <button class="text-blue-500 hover:text-blue-700" onclick="showEditForm(${account.id},'${account.accType}', '${account.accName}', ${account.accAmount}, '${account.accDate}')">แก้ไข</button>
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <button class="text-red-500 hover:text-red-700" onclick="deleteAccount(${account.id})">ลบ</button>
                    </td>
                `;
                tableBody.appendChild(row);

                if (account.accType === 'รายรับ') {
                    income += parseFloat(account.accAmount) || 0;
                } else if (account.accType === 'รายจ่าย') {
                    expense += parseFloat(account.accAmount) || 0;
                }
            });

            // Update total summary cards with filtered data
            document.getElementById('total-income').innerText = income.toFixed(2) + ' บาท';
            document.getElementById('total-expense').innerText = expense.toFixed(2) + ' บาท';
            document.getElementById('total-balance').innerText = (income - expense).toFixed(2) + ' บาท';

        } catch (error) {
            alert('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message);
        }
    }

    async function addAccount(account) {
        try {
            console.log('Adding account:', account);

            const response = await fetch('/api/accounts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(account),
            });

            console.log('Response status:', response.status);

            if (response.ok) {
                const result = await response.json();
                console.log('Account added successfully:', result);
                await fetchData(); // Re-fetch data to update the table with the new entry
                hideAddForm();
                resetForm();
                alert('บันทึกข้อมูลเรียบร้อย');
            } else {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' + errorText);
            }
        } catch (error) {
            console.error('Network error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
        }
    }

    function handleEditSubmit(event) {
        event.preventDefault();
        const form = document.getElementById('edit-account-form');
        const formData = new FormData(form);

        const account = {
            id: formData.get('id'),
            accType: formData.get('accType'),
            accName: formData.get('accName').trim(),
            accAmount: parseFloat(formData.get('accAmount')),
            accDate: formData.get('accDate'),
        };

        console.log('Account data:', account);
        editAccount(account);
    }

    function handleFormSubmit(event) {
        event.preventDefault();
        console.log('Form submitted');

        const form = document.getElementById('account-form');
        const formData = new FormData(form);

        const account = {
            accType: formData.get('accType'),
            accName: formData.get('accName').trim(),
            accAmount: parseFloat(formData.get('accAmount')),
            accDate: formData.get('accDate'),
        };

        if (!account.accName) {
            alert('กรุณากรอกชื่อรายการ');
            return;
        }

        if (isNaN(account.accAmount) || account.accAmount <= 0) {
            alert('กรุณากรอกจำนวนเงินที่ถูกต้อง');
            return;
        }

        if (!account.accDate) {
            alert('กรุณาเลือกวันที่');
            return;
        }

        addAccount(account);
    }

    function showAddForm() {
        console.log('Showing add form');
        document.getElementById('modal').style.display = 'flex';
    }

    function hideAddForm() {
        console.log('Hiding add form');
        document.getElementById('modal').style.display = 'none';
    }

    function resetForm() {
        document.getElementById('account-form').reset();
    }

    function editAccount(account) {
        console.log('Editing account:', account.id);
        fetch(`/api/accounts/${account.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(account),
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Failed to edit account');
            }
        })
        .then(result => {
            console.log('Account edited successfully:', result);
            hideEditForm();
            fetchData(); // Re-fetch data to update the table after editing
            alert('แก้ไขข้อมูลเรียบร้อย');
        })
        .catch(error => {
            console.error('Error editing account:', error);
            alert('เกิดข้อผิดพลาดในการแก้ไขข้อมูล: ' + error.message);
        });
    }

    // Ensure the form submission for adding accounts is handled
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('account-form');
        if (form) {
            form.addEventListener('submit', handleFormSubmit);
        }
    });

    function showEditForm(accId, accType, accName, accAmount, accDate) {
        // The date from the backend might include time, so we split it to get just "YYYY-MM-DD"
        const formattedDate = accDate.split('T')[0];
        document.getElementById('edit-accId').value = accId;
        document.getElementById('edit-modal').style.display = 'flex';
        document.getElementById('edit-accType').value = accType;
        document.getElementById('edit-accName').value = accName;
        document.getElementById('edit-accAmount').value = accAmount;
        document.getElementById('edit-accDate').value = formattedDate;
    }

    function hideEditForm() {
        document.getElementById('edit-modal').style.display = 'none';
    }

    async function deleteAccount(id){
        if (!confirm('คุณแน่ใจว่าต้องการลบรายการนี้หรือไม่?')) {
            return; // Stop if the user cancels
        }
        const response = await fetch(`/api/accounts/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        if (response.ok) {
            const result = await response.json();
            console.log('Account deleted successfully:', result);
            await fetchData(); // Re-fetch data to update the table after deletion
            alert('ลบข้อมูลเรียบร้อย');
        } else {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            alert('เกิดข้อผิดพลาดในการลบข้อมูล: ' + errorText);
        }
    }

</script>