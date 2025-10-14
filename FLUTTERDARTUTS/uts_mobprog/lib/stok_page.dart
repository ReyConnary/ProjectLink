import 'dart:convert'; 
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http; 
import 'package:intl/intl.dart'; 
import 'home_page.dart';
/* mengimpor library yang dibutuhkan :
  material.dart -> komponen ui flutter
  convert.dart -> mengubah data ke format json
  http.dart -> melakukan permintaan http
  intl.dart -> format mata uang
  home_page.dart -> memanggil file halaman utama aplikasi 
*/

class StokPage extends StatefulWidget {
  const StokPage({super.key});

  @override
  _StokPageState createState() => _StokPageState();
}

class _StokPageState extends State<StokPage> {
  List barang = [];
  List filteredBarang = [];
  final searchController = TextEditingController();

  // Controller form tambah barang
  final namaController = TextEditingController();
  final hargaController = TextEditingController();
  final supplierController = TextEditingController();
  final deskripsiController = TextEditingController();

  final String baseUrl = "http://localhost/iventory_db";

  final NumberFormat rupiahFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  // Ambil data dari server
  Future<void> fetchData() async {
    try {
      final response = await http.get(Uri.parse("$baseUrl/get_barang.php"));
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data["status"] == "success") {
          setState(() {
            barang = data["data"];
            filteredBarang = barang;
          });
        } else {
          print("Fetch failed: ${data['message']}");
        }
      } else {
        print("HTTP error: ${response.statusCode}");
      }
    } catch (e) {
      print("Error fetchData: $e");
    }
  }

  void filterBarang(String query) {
    setState(() {
      if (query.isEmpty) {
        filteredBarang = barang;
      } else {
        final lowerQuery = query.toLowerCase();
        filteredBarang = barang.where((item) {
          final nama = item["nama_barang"]?.toString().toLowerCase() ?? "";
          final supplier = item["supplier"]?.toString().toLowerCase() ?? "";
          return nama.contains(lowerQuery) || supplier.contains(lowerQuery);
        }).toList();
      }
    });
  }

  // Tambah barang tanpa jum_barang
  Future<void> tambahBarang() async {
    if (namaController.text.isEmpty || hargaController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("⚠️ Nama & harga wajib diisi!")),
      );
      return;
    }

    try {
      final String tanggalSekarang =
          DateFormat('yyyy-MM-dd').format(DateTime.now());

      final response = await http.post(
        Uri.parse("$baseUrl/add_barang.php"),
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: {
  "nama_barang": namaController.text,
  "jum_barang": "0",
  "harga": hargaController.text,
  "supplier": supplierController.text.isEmpty ? "-" : supplierController.text,
  "deskripsi": deskripsiController.text,
  "tanggal_input": tanggalSekarang,
},
      );

      if (response.statusCode == 200) {
        final res = json.decode(response.body);
        if (res["status"] == "success") {
          ScaffoldMessenger.of(context)
              .showSnackBar(SnackBar(content: Text("✅ Data tersimpan")));
          _clearFields();
          fetchData();
        } else {
          ScaffoldMessenger.of(context)
              .showSnackBar(SnackBar(content: Text("⚠️ ${res['message']}")));
        }
      }
    } catch (e) {
      print("Error tambah: $e");
    }
  }

  Future<void> editBarang(Map item) async {
    final editNamaController = TextEditingController(text: item['nama_barang']);
    final editHargaController = TextEditingController(text: item['harga']);
    final editSupplierController =
        TextEditingController(text: item['supplier']);
    final editDeskripsiController =
        TextEditingController(text: item['deskripsi'] ?? '');

    await showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text("✏️ Edit Barang"),
          content: SingleChildScrollView(
            child: Column(
              children: [
                _buildDialogField(editNamaController, "Nama Barang"),
                _buildDialogField(editHargaController, "Harga",
                    type: TextInputType.number),
                _buildDialogField(editSupplierController, "Supplier"),
                _buildDialogField(editDeskripsiController, "Deskripsi"),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text("Batal"),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: Colors.teal),
              onPressed: () async {
                try {
                  final response = await http.post(
                    Uri.parse("$baseUrl/update_barang.php"),
                    headers: {"Content-Type": "application/x-www-form-urlencoded"},
                    body: {
                      "id_barang": item['id_barang'].toString(),
                      "nama_barang": editNamaController.text,
                      "harga": editHargaController.text,
                      "supplier": editSupplierController.text.isEmpty
                          ? "-"
                          : editSupplierController.text,
                      "deskripsi": editDeskripsiController.text,
                    },
                  );

                  if (response.statusCode == 200) {
                    final res = json.decode(response.body);
                    if (res["status"] == "success") {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text("✅ Data diperbarui")),
                      );
                      Navigator.pop(context);
                      fetchData();
                    } else {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text("⚠️ ${res['message']}")),
                      );
                    }
                  }
                } catch (e) {
                  print("Error edit: $e");
                }
              },
              child: Text("Simpan"),
            ),
          ],
        );
      },
    );
  }

Future<void> hapusBarang(String id) async {
  bool confirmed = false;

  // Tampilkan peringatan sebelum hapus
  confirmed = await showDialog(
    context: context,
    builder: (context) {
      return AlertDialog(
        title: Row(
          children: [
            Icon(Icons.warning_amber_rounded, color: Colors.red, size: 28),
            SizedBox(width: 8),
            Text("Hapus Barang?"),
          ],
        ),
        content: Text(
          "Barang ini mungkin memiliki transaksi terkait.\n\n"
          "⚠️ Semua transaksi yang berhubungan akan ikut terhapus.\n\n"
          "Apakah Anda yakin ingin melanjutkan?",
          style: TextStyle(fontSize: 15),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text("Batal", style: TextStyle(color: Colors.grey[700])),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
            ),
            onPressed: () => Navigator.pop(context, true),
            child: Text("Ya, Hapus"),
          ),
        ],
      );
    },
  );

  if (confirmed != true) return; // batal dihapus

  try {
    final response = await http.post(
      Uri.parse("$baseUrl/delete_barang.php"),
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: {"id_barang": id},
    );

    print("Response: ${response.body}");

    if (response.statusCode == 200) {
      final res = json.decode(response.body);
      if (res["status"] == "success") {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text("🗑️ Data dan transaksi terkait dihapus")));
        fetchData();
      } else {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text("⚠️ ${res['message']}")));
      }
    }
  } catch (e) {
    print("Error hapus: $e");
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text("⚠️ Gagal menghapus data")),
    );
  }
}

  void _clearFields() {
    namaController.clear();
    hargaController.clear();
    supplierController.clear();
    deskripsiController.clear();
  }

  @override
  void initState() {
    super.initState();
    fetchData();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        leading: IconButton(
          icon: Icon(Icons.arrow_back),
          onPressed: () {
            Navigator.pushAndRemoveUntil(
              context,
              MaterialPageRoute(builder: (context) => HomePage()),
              (route) => false,
            );
          },
        ),
        title: Text("📦 Data Barang"),
        backgroundColor: Colors.teal,
      ),
      body: Column(
        children: [
          // Search
          Padding(
            padding: const EdgeInsets.all(12.0),
            child: TextField(
              controller: searchController,
              onChanged: filterBarang,
              decoration: InputDecoration(
                hintText: "Cari barang atau supplier...",
                prefixIcon: Icon(Icons.search),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
          ),

          // Form Tambah Barang (tanpa jumlah)
          Card(
            margin: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
            elevation: 4,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(10),
            ),
            child: Padding(
              padding: EdgeInsets.all(10),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text("Tambah Barang",
                      style:
                          TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  SizedBox(height: 6),
                  _buildCompactField(namaController, "Nama Barang"),
                  _buildCompactField(hargaController, "Harga",
                      type: TextInputType.number),
                  _buildCompactField(supplierController, "Supplier"),
                  _buildCompactField(deskripsiController, "Deskripsi"),
                  SizedBox(height: 8),
                  Align(
                    alignment: Alignment.centerRight,
                    child: ElevatedButton.icon(
                      onPressed: tambahBarang,
                      icon: Icon(Icons.add, size: 18),
                      label: Text("Tambah", style: TextStyle(fontSize: 14)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.white,
                        padding:
                            EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          // Daftar barang
          Expanded(
            child: filteredBarang.isEmpty
                ? Center(child: Text("Belum ada barang"))
                : ListView.builder(
                    padding: EdgeInsets.all(12),
                    itemCount: filteredBarang.length,
                    itemBuilder: (context, index) {
                      final item = filteredBarang[index];
                      final supplierValue = (item['supplier'] == null ||
                              item['supplier'].toString().trim().isEmpty)
                          ? "-"
                          : item['supplier'];
                      final hargaFormatted = rupiahFormat.format(
                        double.tryParse(item['harga'].toString()) ?? 0,
                      );

                      return Card(
                        elevation: 3,
                        margin: EdgeInsets.symmetric(vertical: 6),
                        child: ListTile(
                          leading: CircleAvatar(
                            backgroundColor: Colors.teal,
                            child: Text("${index + 1}",
                                style: TextStyle(color: Colors.white)),
                          ),
                          title: Text(item['nama_barang'] ?? 'Tidak diketahui'),
                          subtitle: Text(
                            "Harga: $hargaFormatted | Supplier: $supplierValue",
                          ),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              IconButton(
                                icon: Icon(Icons.edit, color: Colors.blue),
                                onPressed: () => editBarang(item),
                              ),
                              IconButton(
                                icon: Icon(Icons.delete, color: Colors.red),
                                onPressed: () =>
                                    hapusBarang(item['id_barang'].toString()),
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

  Widget _buildCompactField(
    TextEditingController controller,
    String label, {
    TextInputType type = TextInputType.text,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2.0),
      child: TextField(
        controller: controller,
        keyboardType: type,
        decoration: InputDecoration(
          labelText: label,
          isDense: true,
          contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 8),
          border: OutlineInputBorder(),
        ),
      ),
    );
  }

  Widget _buildDialogField(
    TextEditingController controller,
    String label, {
    TextInputType type = TextInputType.text,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6.0),
      child: TextField(
        controller: controller,
        keyboardType: type,
        decoration: InputDecoration(
          labelText: label,
          border: OutlineInputBorder(),
        ),
      ),
    );
  }
}
