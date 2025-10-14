import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
/* mengimpor library yang dibutuhkan :
  material.dart -> komponen ui flutter
  convert.dart -> mengubah data ke format json
  http.dart -> melakukan permintaan http
*/

class RiwayatTransaksi extends StatefulWidget {
  const RiwayatTransaksi({super.key});

  @override
  _RiwayatTransaksiState createState() => _RiwayatTransaksiState();
}

class _RiwayatTransaksiState extends State<RiwayatTransaksi> {
  List transaksiList = []; // Menyimpan semua data transaksi dari server
  List filteredTransaksi = []; // Menyimpan hasil filter pencarian/transaksi
  bool isLoading = true; // Indikator apakah data sedang dimuat
  final searchController = TextEditingController(); // Kontrol input pencarian
  DateTime? selectedDate; // Menyimpan tanggal filter dari DatePicker

  @override
  void initState() {
    super.initState();
    fetchRiwayat(); // Ambil data transaksi saat halaman pertama kali dibuka
  }

  // Fungsi untuk mengambil data riwayat transaksi dari API
  Future<void> fetchRiwayat() async {
    try {
      final res = await http
          .get(Uri.parse("http://localhost/iventory_db/riwayat_transaksi.php"));
      if (res.statusCode == 200) {
        final data = json.decode(res.body);
        if (data["status"] == "success") {
          setState(() {
            transaksiList = data["data"]; // Simpan data transaksi
            filteredTransaksi = transaksiList; // Awalnya tampil semua
            isLoading = false; // Hentikan indikator loading
          });
        } else {
          setState(() => isLoading = false);
        }
      } else {
        setState(() => isLoading = false);
      }
    } catch (e) {
      print("Error fetchRiwayat: $e"); // Tangani error jika gagal fetch
      setState(() => isLoading = false);
    }
  }

  // Fungsi untuk memfilter transaksi berdasarkan teks dan tanggal
  void filterTransaksi() {
    final query = searchController.text.toLowerCase(); // Ambil input pencarian

    setState(() {
      filteredTransaksi = transaksiList.where((item) {
        final idBarang = (item["id_barang"] ?? '').toString().toLowerCase();
        final jenis = (item["jenis"] ?? '').toString().toLowerCase();
        final tanggal = (item["tanggal"] ?? '').toString();

        // mengecek apakah tanggal sesuai filter (jika ada)
        final cocokTanggal = selectedDate == null
            ? true
            : tanggal.startsWith(
                "${selectedDate!.year}-${selectedDate!.month.toString().padLeft(2, '0')}-${selectedDate!.day.toString().padLeft(2, '0')}");

        // mengecek apakah teks pencarian cocok
        final cocokSearch =
            idBarang.contains(query) || jenis.contains(query);

        return cocokTanggal && cocokSearch; // Gabungkan kedua filter
      }).toList();
    });
  }

  // Fungsi untuk memilih tanggal dari kalender
  Future<void> _pickDate(BuildContext context) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: selectedDate ?? DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
      locale: const Locale('id', 'ID'), // Bahasa Indonesia
    );

    if (picked != null) {
      setState(() {
        selectedDate = picked; // Simpan tanggal terpilih
      });
      filterTransaksi(); // Terapkan filter ulang
    }
  }

  // Fungsi untuk menghapus filter tanggal
  void clearDate() {
    setState(() {
      selectedDate = null;
    });
    filterTransaksi(); // Refresh data tanpa filter tanggal
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("📜 Riwayat Transaksi"),
        backgroundColor: Colors.indigo, // Warna AppBar
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator()) // Tampilkan loading
          : Column(
              children: [
                // Bagian pencarian dan filter tanggal
                Padding(
                  padding: EdgeInsets.all(12),
                  child: Column(
                    children: [
                      // Input untuk mencari transaksi
                      TextField(
                        controller: searchController,
                        decoration: InputDecoration(
                          labelText: "Cari berdasarkan ID Barang / Jenis",
                          prefixIcon: Icon(Icons.search),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        onChanged: (value) => filterTransaksi(), // Filter realtime
                      ),
                      SizedBox(height: 10),
                      // Tombol pilih tanggal dan hapus filter
                      Row(
                        children: [
                          Expanded(
                            child: OutlinedButton.icon(
                              icon: Icon(Icons.calendar_month),
                              label: Text(selectedDate == null
                                  ? "Pilih Tanggal"
                                  : "${selectedDate!.day}/${selectedDate!.month}/${selectedDate!.year}"),
                              onPressed: () => _pickDate(context), // Pilih tanggal
                            ),
                          ),
                          SizedBox(width: 8),
                          if (selectedDate != null)
                            IconButton(
                              icon: Icon(Icons.clear, color: Colors.red),
                              onPressed: clearDate, // Hapus filter tanggal
                              tooltip: "Hapus filter tanggal",
                            ),
                        ],
                      ),
                    ],
                  ),
                ),
                // Daftar hasil transaksi
                Expanded(
                  child: filteredTransaksi.isEmpty
                      ? Center(child: Text("Tidak ada hasil")) // Jika tidak ada hasil
                      : ListView.builder(
                          padding: EdgeInsets.all(12),
                          itemCount: filteredTransaksi.length,
                          itemBuilder: (context, index) {
                            final item = filteredTransaksi[index];
                            return Card(
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12),
                              ),
                              elevation: 3, // Efek bayangan kartu
                              margin: EdgeInsets.symmetric(vertical: 6),
                              child: ListTile(
                                leading: CircleAvatar(
                                  backgroundColor: Colors.indigo.shade100,
                                  child: Text(item["id_transaksi"].toString()), // ID transaksi
                                ),
                                title: Text(
  "${item["nama_barang"] ?? "Nama tidak tersedia"}",
  style: TextStyle(fontWeight: FontWeight.bold),
),
subtitle: Column(
  crossAxisAlignment: CrossAxisAlignment.start,
  children: [
    Text("ID Barang: ${item["id_barang"]}"),
    Text("Jenis: ${item["jenis"]}"),
    Text("Jumlah: ${item["jumlah"]}"),
    Text("Tanggal: ${item["tanggal"]}"),
  ],
),
                              ),
                            );
                          },
                        ),
                ),
              ],
            ),
    );
  }
}
