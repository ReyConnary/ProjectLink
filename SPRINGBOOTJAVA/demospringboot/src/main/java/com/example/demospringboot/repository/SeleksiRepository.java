package com.example.demospringboot.repository;

import com.example.demospringboot.entity.Seleksi;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
@Repository
// Interface
public interface SeleksiRepository
extends JpaRepository<Seleksi, Integer> {
}

