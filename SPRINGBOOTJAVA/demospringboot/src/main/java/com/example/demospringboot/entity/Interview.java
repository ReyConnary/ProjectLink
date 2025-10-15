package com.example.demospringboot.entity;

import jakarta.persistence.*;
import java.time.LocalDate;

// sambung ke tabel sql bernama interview
@Entity
@Table(name = "interview")
public class Interview {

    // sambung ke column2 di tabel interview
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int NoInterview;

    @Column(name = "IDPelamar", nullable = false)
    private int IDPelamar;

    @Column(name = "IDPekerja", nullable = false)
    private int IDPekerja;

    @Column(name = "catatan_interview", length = 500)
    private String catatan_interview;

    @Column(name = "hasil_interview", nullable = false)
    private String hasil_interview;

    @Column(name = "tanggal_interview", nullable = false)
    private LocalDate tanggal_interview;

    // Getters Setters
    public int getNoInterview() {
        return NoInterview;
    }

    public void setNoInterview(int noInterview) {
        NoInterview = noInterview;
    }

    public int getIDPelamar() {
        return IDPelamar;
    }

    public void setIDPelamar(int IDPelamar) {
        this.IDPelamar = IDPelamar;
    }

    public int getIDPekerja() {
        return IDPekerja;
    }

    public void setIDPekerja(int IDPekerja) {
        this.IDPekerja = IDPekerja;
    }

    public String getCatatan_interview() {
        return catatan_interview;
    }

    public void setCatatan_interview(String catatan_interview) {
        this.catatan_interview = catatan_interview;
    }

    public String getHasil_interview() {
        return hasil_interview;
    }

    public void setHasil_interview(String hasil_interview) {
        this.hasil_interview = hasil_interview;
    }

    public LocalDate getTanggal_interview() {
        return tanggal_interview;
    }

    public void setTanggal_interview(LocalDate tanggal_interview) {
        this.tanggal_interview = tanggal_interview;
    }
}

