package com.example.demospringboot.entity;

import jakarta.persistence.*;

// sambung ke tabel yg sesuai menggunakan entity dan @Table(name = "pelamar")
@Entity
@Table(name = "pelamar")
public class Pelamar extends User {

    // sambung kolom

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int IDPelamar;

    @Column(name = "posisi_lamar")
    private String posisi_lamar;

    @Column(name = "pengalaman_kerja")
    private String pengalaman_kerja;

    @Transient
    private String complaint;

    public Pelamar() {
        super();
    }

    // Constructor
    public Pelamar(String Nama, String Alamat, Integer NoTelp, 
    String Email, String posisi_lamar, String pengalaman_kerja) {
        super(Nama, Alamat, NoTelp, Email);
        this.posisi_lamar = posisi_lamar;
        this.pengalaman_kerja = pengalaman_kerja;
    }

    // Getters and Setters
    public int getIDPelamar() {
        return IDPelamar;
    }

    public void setIDPelamar(int IDPelamar) {
        this.IDPelamar = IDPelamar;
    }

    public String getPosisi_lamar() {
        return posisi_lamar;
    }

    public void setPosisi_lamar(String posisi_lamar) {
        this.posisi_lamar = posisi_lamar;
    }

    public String getPengalaman_kerja() {
        return pengalaman_kerja;
    }

    public void setPengalaman_kerja(String pengalaman_kerja) {
        this.pengalaman_kerja = pengalaman_kerja;
    }

    public String getComplaint() {
        return complaint;
    }

    public void setComplaint(String complaint) {
        this.complaint = complaint;
    }



    // gunakan interface utk debug nanti
    @Override
    public void komplain(String message) {
        System.out.println("Complaint from " + getNama() + ": " + message);
    }

    public void debugPelamar() {

        if (this.getNama() == null || this.getNama().isEmpty()) {
            komplain("Nama is missing for this Pelamar");
        }
    }

}