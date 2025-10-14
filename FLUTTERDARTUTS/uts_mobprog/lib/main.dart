import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'home_page.dart';
/* mengimpor library yang dibutuhkan :
  material.dart -> komponen ui flutter
  flutter_localizations.Dart -> mendukung bahasa tertentu
  home_page.dart -> memanggil file halaman utama aplikasi 
*/

void main() {
  runApp(InventoryApp());
}
// fungsi utama aplikasi yang menjalankan widget InventoryApp//

class InventoryApp extends StatelessWidget {
  const InventoryApp({super.key});
//mengatur kelas utama aplikasi untuk bersifat stateless//

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Inventory Barang',
      theme: ThemeData(primarySwatch: Colors.teal),
      home: HomePage(),
/*menentukan aplikasi berbasis materiapapp :
  dimana banner debug dihilangkan
  judul aplikasi "Inventory Barang"
  tema utama berwarna teal
  halaman utama diarahkan ke HomePage
*/

      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      supportedLocales: const [
        Locale('en', 'US'),
        Locale('id', 'ID'),
      ],
    );
  }
}
//mengatur dukungan bahasa aplikasi dalam inggris dan indonesia//