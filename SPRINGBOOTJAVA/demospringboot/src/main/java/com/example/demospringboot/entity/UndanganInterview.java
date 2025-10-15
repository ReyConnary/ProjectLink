package com.example.demospringboot.entity;

import jakarta.persistence.*;

// sambung ke tabel yg sesuai menggunakan entity dan @Table(name = "undanganinterview")
@Entity
@Table(name = "undanganinterview")
public class UndanganInterview {
    // sambung kolom

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "KodeInterview")
    private int KodeInterview;

    @Column(name = "LokasiInterview")
    private String LokasiInterview;

    @Column(name = "TanggalInterview")
    private String TanggalInterview;

    @Column(name = "WaktuInterview")
    private String WaktuInterview;

    @Column(name = "IDPekerja")
    private int IDPekerja;

    // Getters and Setters
    public int getKodeInterview() {
        return KodeInterview;
    }

    public void setKodeInterview(int kodeInterview) {
        KodeInterview = kodeInterview;
    }

    public String getLokasiInterview() {
        return LokasiInterview;
    }

    public void setLokasiInterview(String lokasiInterview) {
        LokasiInterview = lokasiInterview;
    }

    public String getTanggalInterview() {
        return TanggalInterview;
    }

    public void setTanggalInterview(String tanggalInterview) {
        TanggalInterview = tanggalInterview;
    }

    public String getWaktuInterview() {
        return WaktuInterview;
    }

    public void setWaktuInterview(String waktuInterview) {
        WaktuInterview = waktuInterview;
    }

    public int getIDPekerja() {
        return IDPekerja;
    }

    public void setIDPekerja(int idPekerja) {
        IDPekerja = idPekerja;
    }
}
