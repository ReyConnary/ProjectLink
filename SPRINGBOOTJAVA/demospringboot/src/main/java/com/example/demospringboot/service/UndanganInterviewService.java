package com.example.demospringboot.service;

import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.example.demospringboot.entity.UndanganInterview;
import com.example.demospringboot.repository.UndanganInterviewRepository;

@Service
public class UndanganInterviewService {

    @Autowired
    private UndanganInterviewRepository undanganInterviewRepository;

    // Find all interview invitations
    public List<UndanganInterview> findAll() {
        return undanganInterviewRepository.findAll();
    }

    // Find an interview invitation by ID
    public Optional<UndanganInterview> findById(int id) {
        return undanganInterviewRepository.findById(id);
    }

    // Save an interview invitation (either add or update)
    public void save(UndanganInterview undangan) {
        undanganInterviewRepository.save(undangan);
    }

    // Update an interview invitation
    public void update(UndanganInterview undangan) {
        undanganInterviewRepository.save(undangan); // Same method for both save and update
    }

    // Delete an interview invitation by ID
    public void delete(UndanganInterview id) {
        undanganInterviewRepository.delete(id);
    }
    
}

