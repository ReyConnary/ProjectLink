package com.example.demospringboot;

import com.example.demospringboot.entity.Pelamar;

public class PelamarTest {

    public static void main(String[] args) {
        
        Pelamar pelamar = new Pelamar("", "123 Street", 123456789, "john@example.com", "Makrer", "short");

        
        pelamar.debugPelamar();  
    }
}
