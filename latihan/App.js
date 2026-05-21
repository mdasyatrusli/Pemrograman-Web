import Student from "./models/Student.js";
import BankAccount from "./models/BankAccount.js";

const mhs1 = new Student("Dasyat", 23, "221120134", "Computer Science");
mhs1.greet();
mhs1.study();

const rekeningMhs = new BankAccount(mhs1.name);
rekeningMhs.deposit(200000);
rekeningMhs.withdraw(500000);

document.getElementById("output").innerHTML = `
<h2>Data Mahasiswa</h2>
<p><strong>Nama:</strong> ${mhs1.name}</p>
<p><strong>Umur:</strong> ${mhs1.age}</p>
<p><strong>NIM:</strong> ${mhs1.nim}</p>
<p><strong>Jurusan:</strong> ${mhs1.major}</p>
<p><strong>Saldo Akhir:</strong>
Rp${rekeningMhs.getBalance().toLocaleString("id-ID")}</p>
`;
