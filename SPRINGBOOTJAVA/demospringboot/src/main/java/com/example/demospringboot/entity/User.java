package com.example.demospringboot.entity;

import jakarta.persistence.Column;
import jakarta.persistence.MappedSuperclass;


// ini adalah abstract class yg membagi atributnya 
// ke pelamar dan pekerja gunakan mapped superclas
@MappedSuperclass
public abstract class User implements Komplainable {

    @Column(name = "Nama")
    private String Nama;

    @Column(name = "Alamat")
    private String Alamat;

    @Column(name = "NoTelp")
    private Integer NoTelp;

    @Column(name = "Email")
    private String Email;

    // Constructor with all fields
    public User(String Nama, String Alamat, Integer NoTelp, String Email) {
        this.Nama = Nama;
        this.Alamat = Alamat;
        this.NoTelp = NoTelp;
        this.Email = Email;
    }

    // Default constructor
    public User() {
    }

    // Getters and setters
    public String getNama() {
        return Nama;
    }

    public void setNama(String Nama) {
        this.Nama = Nama;
    }

    public String getAlamat() {
        return Alamat;
    }

    public void setAlamat(String Alamat) {
        this.Alamat = Alamat;
    }

    public Integer getNoTelp() {
        return NoTelp;
    }

    public void setNoTelp(Integer NoTelp) {
        this.NoTelp = NoTelp;
    }

    public String getEmail() {
        return Email;
    }

    public void setEmail(String Email) {
        this.Email = Email;
    }


    @Override
    public abstract void komplain(String message);


}
