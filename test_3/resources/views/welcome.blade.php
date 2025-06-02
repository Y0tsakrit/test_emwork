<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ระบบรายรับ-รายจ่าย</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding-top: 20px;

        }
    </style>
</head>
<body class="w-[80%] h-screen flex flex-col items-center gap-5 mx-auto">
    <div class="text-white text-4xl">บันทึกรายรับ รายจ่าย</div>
    <div class="text-white text-xl">Income & Expense Tracker</div>
    <div class="flex justify-between flex-row gap-7">
        <div id="home" class="text-black bg-white border border-transparent w-[150px] h-[75px] flex items-center justify-center text-center rounded-2xl cursor-pointer hover:-translate-y-2 transition-transform duration-300">บันทึกรายการ</div>
        <div id="report" class="text-white bg-[#FFFFFF33] border border-transparent w-[150px] h-[75px] flex items-center justify-center text-center rounded-2xl cursor-pointer hover:-translate-y-2 transition-transform duration-300">รายงาน</div>
    </div>
    <div class="flex flex-row justify-center border border-transparent w-[400px] h-[75px] rounded-2xl bg-gray-100 items-center gap-5">
        <div class="text-black text-lg">เลือกเดือน:</div>
        <input id="datePicker" type="month" class="w-[200px] h-[50px] rounded-2xl text-black px-2" onchange="fetchData()">
        <div id="refresh" class="text-2xl cursor-pointer" onclick="document.getElementById('datePicker').value = ''; fetchData();">🔄</div>
    </div>
    <div class="flex flex-col items-center py-5 px-12 gap-5 w-[1300px] h-fit bg-gray-100 rounded-2xl">
        <div class=" bg-gray-200 h-fit w-full items-center rounded-2xl p-5">
            <form class="flex flex-col gap-5" onsubmit="handleSubmit(event)">
                <div class="flex justify-center text-2xl">เพิ่มรายการใหม่</div>
                <div class="flex flex-row items-center gap-5 py-2">
                    <div class="flex flex-col gap-2 w-[50%]">
                        <label for="type" class="text-black text-lg">ประเภท:</label>
                        <select id="type" class="w-full h-[50px] rounded-2xl text-black px-2">
                            <option value="รายรับ">รายรับ</option>
                            <option value="รายจ่าย">รายจ่าย</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 w-[50%]">
                        <label for="amount" class="text-black text-lg">จำนวน:</label>
                        <input id="amount" type="text" class="w-full h-[50px] rounded-2xl text-black px-2" placeholder="จำนวนเงิน">
                    </div>
                </div>
                <div class="flex flex-row items-center gap-5 py-2">
                    <div class="flex flex-col gap-2 w-[50%]">
                        <label for="date" class="text-black text-lg">วันที่:</label>
                        <input id="date" type="date" class="w-full h-[50px] rounded-2xl text-black px-2" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="flex flex-col gap-2 w-[50%]">
                        <label for="description" class="text-black text-lg">รายละเอียด:</label>
                        <input id="description" type="text" class="w-full h-[50px] rounded-2xl text-black px-2" placeholder="รายละเอียดเพิ่มเติม">
                    </div>
                </div>
                <button id="submit" type="submit" class="w-[100px] h-[50px] bg-[#764ba2] text-white rounded-2xl flex items-center justify-center mx-auto hover:-translate-y-2 transition-transform duration-300">บันทึก</button>
            </form>
        </div>
        <table id="dataTable" class="w-full text-center border-collapse overflow-hidden bg-white shadow-lg rounded-2xl">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-[#764ba2] text-white border-r border-white">วันที่</th>
                    <th class="py-3 px-4 bg-[#764ba2] text-white border-r border-white">ประเภท</th>
                    <th class="py-3 px-4 bg-[#764ba2] text-white border-r border-white">จำนวน</th>
                    <th class="py-3 px-4 bg-[#764ba2] text-white border-r border-white">รายละเอียด</th>
                    <th class="py-3 px-4 bg-[#764ba2] text-white border-r border-white">แก้ไข</th>
                    <th class="py-3 px-4 bg-[#764ba2] text-white border-r border-white">ลบ</th>
                </tr>
            </thead>
            <tbody id="dataBody" class="text-black">
                <!-- Data  -->
            </tbody>
        </table>
    </div>

    <div class="fixed inset-0 w-screen h-screen z-[1000] justify-center items-center" style="background-color: rgba(17, 24, 39, 0.8); display: none;" id="editFormContainer">
        <form class="flex flex-col gap-5 bg-gray-100 p-5 rounded-2xl w-[70%]" onsubmit="handleEdit(event)">
            <div class="flex justify-center text-2xl">แก้ไขรายการ</div>
            <input type="hidden" id="editId">
            <div class="flex flex-row items-center gap-5 py-2">
                <div class="flex flex-col gap-2 w-[50%]">
                    <label for="editType" class="text-black text-lg">ประเภท:</label>
                    <select id="editType" class="w-full h-[50px] rounded-2xl text-black px-2">
                        <option value="รายรับ">รายรับ</option>
                        <option value="รายจ่าย">รายจ่าย</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2 w-[50%]">
                    <label for="editAmount" class="text-black text-lg">จำนวน:</label>
                    <input id="editAmount" type="text" class="w-full h-[50px] rounded-2xl text-black px-2" placeholder="จำนวนเงิน">
                </div>
            </div>
            <div class="flex flex-row items-center gap-5 py-2">
                <div class="flex flex-col gap-2 w-[50%]">
                    <label for="editDate" class="text-black text-lg">วันที่:</label>
                    <input id="editDate" type="date" class="w-full h-[50px] rounded-2xl text-black px-2" value="{{ date('Y-m-d') }}">
                </div>
                <div class="flex flex-col gap-2 w-[50%]">
                    <label for="editDescription" class="text-black text-lg">รายละเอียด:</label>
                    <input id="editDescription" type="text" class="w-full h-[50px] rounded-2xl text-black px-2" placeholder="รายละเอียดเพิ่มเติม">
                </div>
            </div>
            <button id="submit" type="submit" class="w-[100px] h-[50px] bg-[#764ba2] text-white rounded-2xl flex items-center justify-center mx-auto hover:-translate-y-2 transition-transform duration-300">บันทึก</button>
        </form>
    </div>

</body>
</html>



<script>
    const init = () => {
        const home = document.getElementById('home');
        const report = document.getElementById('report');
        const url = window.location.href;

        console.log((url.split('/'))[3]);
        try{
            if((url.split('/'))[3] == 'report'){
                report.style.backgroundColor = '#FFFFFF';
                report.style.color = '#000000';
                home.style.backgroundColor = '#FFFFFF33';
                home.style.color = '#FFFFFF';
            }
        }catch(e){
            home.style.backgroundColor = '#FFFFFF';
            home.style.color = '#000000';
            report.style.backgroundColor = '#FFFFFF33';
            report.style.color = '#FFFFFF';
        }
    }

    init();

    const home = document.getElementById('home');
    const report = document.getElementById('report');
    home.addEventListener('click', () => {
        window.location.href = '/';
    });
    report.addEventListener('click', () => {
        window.location.href = '/report';
    });

    const formatDate = (date) => {
        const d = new Date(date);
        return `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
    };

    const fetchData = () => {

        const datePicker = document.getElementById('datePicker');
        let url = '';
        if (datePicker.value) {
            const selectedDate = datePicker.value;
            url = "?month="+selectedDate;
        }
        fetch(`api/accounts${url}`)
            .then(response => response.json())
            .then(data => {
                const dataBody = document.getElementById('dataBody');
                dataBody.innerHTML = '';
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="py-3 px-4 border-b border-gray-200">${item.accType}</td>
                        <td class="py-3 px-4 border-b border-gray-200">${formatDate(item.accDate)}</td>
                        <td class="py-3 px-4 border-b border-gray-200">${item.accAmount}</td>
                        <td class="py-3 px-4 border-b border-gray-200">${item.accName}</td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            <button class="text-blue-500" onclick="showEditForm(${item.id}, '${item.accType}', ${item.accAmount}, '${item.accDate}', '${item.accName}')">🛠️</button>
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            <button class="text-red-500 " onclick="deleteItem(${item.id})">❌</button>
                        </td>
                    `;
                    dataBody.appendChild(row);
                });
            });
    };



    fetchData();

    const handleSubmit = async (event) => {
        event.preventDefault();
        const type = document.getElementById('type').value;
        const amount = parseFloat(document.getElementById('amount').value);
        console.log(type, amount);
        const date = document.getElementById('date').value;
        const description = document.getElementById('description').value;

        console.log(type, amount, date, description);
        if (!amount || !date) {
            alert('กรุณากรอกจำนวนและวันที่');
            return;
        }

        try{
            const response = await fetch('api/accounts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    accType: type,
                    accAmount: amount,
                    accDate: date,
                    accName: description,
                }),
            });
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            alert('บันทึกสำเร็จ');
            fetchData();
        } catch (error) {
            alert('There was a problem with the fetch operation:', error.message);
        }
    };

    const deleteItem = async (id) => {
        if (!confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?')) return;

        try {
            const response = await fetch(`api/accounts/${id}`, {
                method: 'DELETE',
            });
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            alert('ลบรายการสำเร็จ');
            fetchData();
        } catch (error) {
            alert('There was a problem with the fetch operation:', error.message);
        }
};

    const showEditForm = (id, type, amount, date, description) => {
        const editFormContainer = document.getElementById('editFormContainer');
        editFormContainer.style.display = 'flex';
        console.log(id, type, amount, date, description);
        document.getElementById('editId').value = id;
        document.getElementById('editType').value = type;
        document.getElementById('editAmount').value = amount;
        document.getElementById('editDate').value = date.split('T')[0]; 
        document.getElementById('editDescription').value = description;
    };

    const handleEdit = async (event) => {
        event.preventDefault();
        const id = document.getElementById('editId').value;
        const type = document.getElementById('editType').value;
        const amount = parseFloat(document.getElementById('editAmount').value);
        const date = document.getElementById('editDate').value;
        const description = document.getElementById('editDescription').value;

        if (!amount || !date) {
            alert('กรุณากรอกจำนวนและวันที่');
            return;
        }

        try {
            const response = await fetch(`api/accounts/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    accType: type,
                    accAmount: amount,
                    accDate: date,
                    accName: description,
                }),
            });
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            alert('แก้ไขรายการสำเร็จ');
            fetchData();
            document.getElementById('editFormContainer').style.display = 'none';
        } catch (error) {
            alert('There was a problem with the fetch operation:', error.message);
        }
    };

</script>