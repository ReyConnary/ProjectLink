package com.example.demospringboot.repository;

import com.example.demospringboot.entity.Pekerja;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
@Repository
// Interface
public interface PekerjaRepository
extends JpaRepository<Pekerja, Integer> {
}
