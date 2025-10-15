package com.example.demospringboot.entity;

import jakarta.persistence.*;

// sambung ke tabel yg sesuai menggunakan entity dan @Table(name = "pekerja")
@Entity
@Table(name = "pekerja")
public class Pekerja extends User {

    // sambung ke kolom yg sesuai

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int IDPekerja;

    @Column(name = "Posisi", nullable = false)
    private String Posisi;

    @Column(name = "Password", nullable = false)
    private String Password;

    @Transient
    private String complaint;

    public Pekerja() {
        super();
    }

    // Constructor
    public Pekerja(String Nama, String Alamat, Integer NoTelp, String Email, String Posisi, String Password) {
        super(Nama, Alamat, NoTelp, Email);
        this.Posisi = Posisi;
        this.Password = Password;
    }

    // Getter Setters
    public int getIDPekerja() {
        return IDPekerja;
    }

    public void setIDPekerja(int IDPekerja) {
        this.IDPekerja = IDPekerja;
    }

    public String getPosisi() {
        return Posisi;
    }

    public void setPosisi(String Posisi) {
        this.Posisi = Posisi;
    }

    public String getComplaint() {
        return complaint;
    }

    public void setComplaint(String complaint) {
        this.complaint = complaint;
    }

    public String getPassword() {
        return Password;
    }

    public void setPassword(String password) {
        Password = password;
    }


    // gunakan interface
    @Override
    public void komplain(String message) {
        System.out.println("Complaint from " + getNama() + ": " + message);
    }

    // untuk debug di PekerjaTest
    public void debugPekerja() {

        if (this.getNama() == null || this.getNama().isEmpty()) {
            komplain("Nama is missing for this Pekerja: ");
        }
    }

    

}