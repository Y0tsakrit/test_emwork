import Express from 'express';
import cors from 'cors';
import mysql from 'mysql2/promise';

const app = Express();
const PORT = process.env.PORT || 3000;


app.use(cors());
app.use(Express.json());

// MySQL connection
const dbConfig = {
  host: 'localhost',
  user: 'root',
  password: 'tonkla2550',
  database: 'my_db'
};

let connection;
async function connectToDatabase() {
  try {
    connection = await mysql.createConnection(dbConfig);
    console.log('Connected to MySQL database');
  } catch (error) {
    console.error('Error connecting to MySQL database:', error);
  }
}

connectToDatabase();

app.post('/api/save', async (req, res) => {
   const {
    name = null,
    department = null,
    email = null,
    phone = null,
    type = "อื่นๆ", 
    reason = null,
    startDate = null,
    endDate = null
  } = req.body;

  if (!type){
    type = "อื่นๆ";
  }
  const today = new Date();
  const start = new Date(startDate);
  const end = new Date(endDate);


  if (start >= end) {
    return res.status(400).json({ error: 'วันที่เริ่มต้นต้องน้อยกว่าวันที่สิ้นสุด' });
  }


  if (start < today.setHours(0, 0, 0, 0)) {
    return res.status(400).json({ error: 'ไม่อนุญาติให้บันทึกวันลาย้อนหลัง' });
  }

  if (type === "ลาพักร้อน") {
    const daysUntilStart = Math.ceil((start - today) / (1000 * 60 * 60 * 24)); 
    const leaveDuration = Math.ceil((end - start) / (1000 * 60 * 60 * 24)); 
    if (daysUntilStart < 3) {
      return res.status(400).json({ error: 'กรณีพักร้อนลาล่วงหน้าอย่างน้อย 3 วัน' });
    }

    if (leaveDuration > 2) {
      return res.status(400).json({ error: 'กรณีพักร้อนลาติดต่อกันได้ไม่เกิน 2 วัน' });
    }
  }

  try {
    const [result] = await connection.execute(
      `INSERT INTO leave_requests (
         full_name, position, email, phone, leave_type, reason, start_date, end_date
       ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [name, department, email, phone, type, reason, startDate, endDate]
    );

    res.status(201).json({ id: result.insertId, ...req.body });
  } catch (error) {
    console.error('Error saving leave:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

app.get('/api/leaves', async (req, res) => {
  try {
    const [rows] = await connection.execute('SELECT * FROM leave_requests');
    res.status(200).json(rows);
  } catch (error) {
    console.error('Error fetching leaves:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

app.patch('/api/change', async (req, res) => {
  const { id,status } = req.body;

  const [result] = await connection.execute(
    'SELECT status FROM leave_requests WHERE id = ?',
    [id]
  );
  if (result[0].status == "อนุมัติ" || result[0].status == "ไม่อนุมัติ") {
    return res.status(400).json({ error: 'ไม่สามารถเปลี่ยนสถานะได้อีก' });
  }
  try {
    const [result] = await connection.execute(
      'UPDATE leave_requests SET status = ? WHERE id = ?',
      [status, id]
    );

    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'ไม่พบคำขอลา' });
    }

    res.status(200).json({ message: 'อัพเดตสถานะสำเร็จ' });
  } catch (error) {
    console.error('Error updating leave status:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

app.delete('/api/delete', async (req, res) => {
  const { id } = req.body;

  try {
    const [result] = await connection.execute(
      'DELETE FROM leave_requests WHERE id = ?',
      [id]
    );

    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'ไม่พบคำขอลา' });
    }

    res.status(200).json({ message: 'ลบคำขอลาเรียบร้อยแล้ว' });
  } catch (error) {
    console.error('Error deleting leave request:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});


app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
