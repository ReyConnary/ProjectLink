package com.example.demospringboot;

import com.example.demospringboot.entity.Pekerja;

public class PekerjaTest {

    public static void main(String[] args) {
        Pekerja pekerja = new Pekerja("", "123 Street", 123456789, "john@example.com", "Makrer", "short");

       
        pekerja.debugPekerja();
    }
}
