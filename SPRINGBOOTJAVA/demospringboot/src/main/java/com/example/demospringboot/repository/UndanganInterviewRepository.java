package com.example.demospringboot.repository;

import com.example.demospringboot.entity.UndanganInterview;


import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface UndanganInterviewRepository extends JpaRepository<UndanganInterview, Integer> 
{
}

