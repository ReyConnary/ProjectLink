package com.example.demospringboot.repository;

import com.example.demospringboot.entity.Pelamar;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
@Repository
// Interface
public interface PelamarRepository
extends JpaRepository<Pelamar, Integer> {
}

