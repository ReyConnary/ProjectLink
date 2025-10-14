import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart'; 
/* 
Mengimpor library:
- material.dart → untuk UI Flutter
- convert.dart → konversi JSON
- http.dart → komunikasi HTTP API
- intl.dart → format angka/mata uang
*/

class HistoriPage extends StatefulWidget {
  const HistoriPage({super.key});

  @override
  _HistoriPageState createState() => _HistoriPageState();
}

class _HistoriPageState extends State<HistoriPage> {
  List barangList = [];        // Menyimpan semua data barang dari API
  List filteredList = [];      // Menyimpan data setelah difilter pencarian
  bool isLoading = true;       // Indikator loading
  final searchController = TextEditingController(); // Controller untuk kolom pencarian

  final NumberFormat rupiahFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  ); // Format angka ke dalam format mata uang Rupiah

  @override
  void initState() {
    super.initState();
    fetchBarang(); // Panggil fungsi untuk ambil data barang saat halaman dibuka
  }

  Future<void> fetchBarang() async {
    try {
      // Ambil data barang dari API lokal
      final res = await http.get(Uri.parse("http://localhost/iventory_db/get_barang.php"));
      if (res.statusCode == 200) {
        final data = json.decode(res.body); // Decode data JSON
        if (data["status"] == "success") {
          setState(() {
            barangList = data["data"];    // Simpan semua data ke variabel
            filteredList = barangList;    // Awalnya tampilkan semua data
            isLoading = false;
          });
        } else {
          setState(() => isLoading = false);
        }
      } else {
        setState(() => isLoading = false);
      }
    } catch (e) {
      print("Error fetchBarang: $e"); // Tampilkan error jika gagal ambil data
      setState(() => isLoading = false);
    }
  }

  void filterBarang() {
    // Filter barang berdasarkan input pencarian
    final query = searchController.text.toLowerCase();
    setState(() {
      filteredList = barangList.where((item) {
        final nama = (item["nama_barang"] ?? '').toString().toLowerCase();
        final supplier = (item["supplier"] ?? '').toString().toLowerCase();
        final id = (item["id_barang"] ?? '').toString().toLowerCase();
        // Cocokkan query dengan nama, supplier, atau ID barang
        return nama.contains(query) || supplier.contains(query) || id.contains(query);
      }).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("📦 Histori Barang"),
        backgroundColor: Colors.indigo,
      ),
      body: isLoading
          // Tampilkan loading spinner saat data masih dimuat
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                // Kolom pencarian
                Padding(
                  padding: const EdgeInsets.all(12.0),
                  child: TextField(
                    controller: searchController,
                    decoration: InputDecoration(
                      labelText: "Cari berdasarkan nama, supplier, atau ID",
                      prefixIcon: const Icon(Icons.search),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    onChanged: (value) => filterBarang(), // Jalankan filter setiap perubahan teks
                  ),
                ),
                // Daftar data barang
                Expanded(
                  child: filteredList.isEmpty
                      ? const Center(child: Text("Tidak ada hasil")) // Jika hasil kosong
                      : ListView.builder(
                          padding: const EdgeInsets.all(12),
                          itemCount: filteredList.length,
                          itemBuilder: (context, index) {
                            final item = filteredList[index];
                            // Format harga ke Rupiah
                            final hargaFormatted = rupiahFormat.format(
                              double.tryParse(item["harga"].toString()) ?? 0,
                            ); 

                            return Card(
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12),
                              ),
                              elevation: 3,
                              margin: const EdgeInsets.symmetric(vertical: 6),
                              child: ListTile(
                                // Lingkaran kecil di sebelah kiri dengan ID barang
                                leading: CircleAvatar(
                                  backgroundColor: Colors.indigo.shade100,
                                  child: Text(
                                    item["id_barang"].toString(),
                                    style: const TextStyle(fontSize: 12),
                                  ),
                                ),
                                // Nama barang
                                title: Text(
                                  item["nama_barang"] ?? "Nama tidak tersedia",
                                  style: const TextStyle(fontWeight: FontWeight.bold),
                                ),
                                // Detail tambahan barang
                                subtitle: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text("ID: ${item["id_barang"] ?? "-"}"),
                                    Text("Jumlah: ${item["jum_barang"] ?? "0"}"),
                                    Text("Harga: $hargaFormatted"), 
                                    Text("Supplier: ${item["supplier"] ?? "-"}"),
                                    Text("Tanggal Input: ${item["tanggal_input"] ?? "-"}"),
                                    const SizedBox(height: 4),
                                    Text(
                                      "Deskripsi: ${item["deskripsi"] ?? "-"}",
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis, // Potong teks panjang
                                    ),
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

