package com.example.demospringboot.entity;

import jakarta.persistence.*;

// sambung ke tabel yg sesuai menggunakan entity dan @Table(name = "selesi")
@Entity
@Table(name = "seleksi")
public class Seleksi {

    //sambung kolom
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "KodeSeleksi")
    private int kodeSeleksi;

    @Column(name = "Keputusan")
    private String keputusan;

    @Column(name = "TanggalKeputusan")
    private String tanggalKeputusan;

    @Column(name = "IDPelamar")
    private String iDPelamar;

    public int getKodeSeleksi() {
        return kodeSeleksi;
    }

    public void setKodeSeleksi(int kodeSeleksi) {
        this.kodeSeleksi = kodeSeleksi;
    }

    public String getKeputusan() {
        return keputusan;
    }

    public void setKeputusan(String keputusan) {
        this.keputusan = keputusan;
    }

    public String getTanggalKeputusan() {
        return tanggalKeputusan;
    }

    public void setTanggalKeputusan(String tanggalKeputusan) {
        this.tanggalKeputusan = tanggalKeputusan;
    }

    public String getiDPelamar() {
        return iDPelamar;
    }

    public void setiDPelamar(String iDPelamar) {
        this.iDPelamar = iDPelamar;
    }

    
}

