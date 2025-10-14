import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'stok_page.dart';
import 'laporan_page.dart';
import 'halaman_transaksi.dart';
import 'riwayat_transaksi.dart';
import 'histori.dart'; 
/* mengimpor library yang dibutuhkan :
  material.dart -> komponen ui flutter
  convert.dart -> mengubah data ke format json
  http.dart -> melakukan permintaan http
  intl.dart -> format mata uang
  stok_page.dart -> memanggil halaman stok barang
  laporan_page.dart -> memanggil halaman laporan barang
  halaman_transaksi.dart -> memanggil halaman transaksi barang
  riwayat_transaksi.dart -> memanggil halaman riwayat transaksi
  histori.dart -> memanggil halaman histori barang
*/

// menghubungkan halaman lain ke halaman utama aplikasi
class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  _HomePageState createState() => _HomePageState();
}

// mengatur homepage sebagai widget utama yang menampilkan dashboard serta berubah sesuai data yang diterima dari server
class _HomePageState extends State<HomePage> {
  // menyimpan data dashboard dari server API
  Map<String, dynamic>? dashboardData;
  bool loading = true;

  // Format angka ke bentuk mata uang Rupiah
  final NumberFormat rupiahFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  @override
  void initState() {
    super.initState();
    fetchDashboardData();
  }

  // mengambil data dari server API dan mengubahnya ke format json
  Future<void> fetchDashboardData() async {
    try {
      final response = await http.get(
        Uri.parse("http://localhost/iventory_db/get_dashboard_data.php"),
      );

      if (response.statusCode == 200) {
        setState(() {
          dashboardData = json.decode(response.body);
          loading = false;
        });
      } else {
        throw Exception("Failed to load data");
      }
    } catch (e) {
      print("Error: $e");
      setState(() => loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text("📊 Aplikasi Manajemen Inventaris"),
        centerTitle: true,
        backgroundColor: Colors.teal,
      ),
      body: loading
          ? const Center(child: CircularProgressIndicator())
          : dashboardData == null
              ? const Center(child: Text("Gagal memuat data"))
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    _buildCard(
                      "📦 Total Barang",
                      "${dashboardData!['total_barang']}",
                      Colors.teal,
                    ),
                    _buildCard(
                      "📥 Barang Masuk Hari Ini",
                      "${dashboardData!['barang_masuk_hari_ini']}",
                      Colors.blue,
                    ),
                    _buildCard(
                      "📤 Barang Keluar Hari Ini",
                      "${dashboardData!['barang_keluar_hari_ini']}",
                      Colors.red,
                    ),
                    _buildCard(
                      "💰 Nilai Total Stok",
                      rupiahFormat.format(
                        double.tryParse(
                              dashboardData!['nilai_stok'].toString(),
                            ) ??
                            0,
                      ),
                      Colors.orange,
                    ),
                    const SizedBox(height: 20),
                    const Text(
                      "⚠️ Stok Hampir Habis",
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    ..._buildLowStockList(dashboardData!['stok_hampir_habis']),
                    const SizedBox(height: 20),

                    // Menu navigasi ke berbagai halaman
                    ListTile(
                      leading: const Icon(Icons.swap_horiz, color: Colors.indigo),
                      title: const Text("Kelola Transaksi Barang"),
                      trailing: const Icon(Icons.arrow_forward_ios),
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const HalamanTransaksi()),
                      ),
                    ),
                    ListTile(
                      leading: const Icon(Icons.inventory, color: Colors.teal),
                      title: const Text("Kelola Data Barang"),
                      trailing: const Icon(Icons.arrow_forward_ios),
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const StokPage()),
                      ),
                    ),
                    ListTile(
                      leading: const Icon(Icons.bar_chart, color: Colors.deepOrange),
                      title: const Text("Laporan Barang"),
                      trailing: const Icon(Icons.arrow_forward_ios),
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const LaporanPage()),
                      ),
                    ),
                    ListTile(
                      leading: const Icon(Icons.history, color: Colors.purple),
                      title: const Text("Riwayat Transaksi"),
                      trailing: const Icon(Icons.arrow_forward_ios),
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const RiwayatTransaksi()),
                      ),
                    ),

                    // Tombol baru menuju halaman histori
                    ListTile(
                      leading: const Icon(Icons.list_alt, color: Colors.green),
                      title: const Text("Histori Barang (Data Lengkap)"),
                      trailing: const Icon(Icons.arrow_forward_ios),
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const HistoriPage()),
                      ),
                    ),
                  ],
                ),
    );
  }

  // Membuat kartu ringkasan
  Widget _buildCard(String title, String value, Color color) {
    return Card(
      elevation: 4,
      margin: const EdgeInsets.symmetric(vertical: 8),
      child: ListTile(
        title: Text(title),
        subtitle: Text(value, style: TextStyle(fontSize: 18, color: color)),
      ),
    );
  }

  // Menampilkan daftar stok hampir habis
  List<Widget> _buildLowStockList(List<dynamic> lowStock) {
    if (lowStock.isEmpty) {
      return [const Text("Semua stok aman ✅")];
    }

    return lowStock.map((item) {
      final stok = item['jum_barang'] ?? item['stok'] ?? 0;
      return Card(
        margin: const EdgeInsets.symmetric(vertical: 4),
        child: ListTile(
          leading: const Icon(Icons.warning, color: Colors.redAccent),
          title: Text(item['nama_barang'] ?? 'Tidak diketahui'),
          subtitle: Text("Sisa stok: $stok"),
        ),
      );
    }).toList();
  }
}
