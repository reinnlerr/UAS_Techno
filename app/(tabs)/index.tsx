import { router } from 'expo-router';
import React, { useState } from 'react';
import { SafeAreaView, ScrollView, StatusBar, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native';

export default function App() {
  const [teamName, setTeamName] = useState('');
  const [date, setDate] = useState('');
  const [selectedTime, setSelectedTime] = useState('19:00');

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#f7faf9" />
      <ScrollView contentContainerStyle={styles.scrollContent}>
        
        {/* Header Section */}
        <View style={styles.header}>
          <Text style={styles.headerTitle}>FasilBook</Text>
          <Text style={styles.headerSubtitle}>Reservasi Pertandingan</Text>
        </View>

        {/* Form Card */}
        <View style={styles.card}>
          <Text style={styles.sectionTitle}>Detail Pesanan</Text>

          {/* Time Slots Interaktif */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>Pilih Jam (Malam)</Text>
            <View style={styles.timeSlotContainer}>
              {['19:00', '20:00', '21:00'].map((time) => (
                <TouchableOpacity 
                  key={time}
                  style={selectedTime === time ? styles.timeSlotActive : styles.timeSlot}
                  onPress={() => setSelectedTime(time)}
                >
                  <Text style={selectedTime === time ? styles.timeSlotTextActive : styles.timeSlotText}>
                    {time}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>

          {/* Input Tanggal */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>Tanggal Main</Text>
            <TextInput
              style={styles.input}
              placeholder="DD/MM/YYYY"
              placeholderTextColor="#b2b3b5"
              value={date}
              onChangeText={setDate}
            />
          </View>
        </View>

        {/* CTA Button */}
        <TouchableOpacity 
          style={styles.button} 
          onPress={() => router.push('/E-tiket')}
        >
          <Text style={styles.buttonText}>Konfirmasi & Bayar</Text>
        </TouchableOpacity>
        
      </ScrollView>
    </SafeAreaView>
  );
}

// Bagian CSS ala React Native
const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f7faf9', // surface color dari desainmu
  },
  scrollContent: {
    padding: 20,
    paddingTop: 40,
  },
  header: {
    marginBottom: 24,
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#006d37', // Pitch Green
  },
  headerSubtitle: {
    fontSize: 16,
    color: '#5d5e61',
    marginTop: 4,
  },
  card: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 20,
    borderWidth: 1,
    borderColor: '#e6e9e8',
    marginBottom: 24,
    elevation: 2, // Efek shadow tipis di Android
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#181c1c',
    marginBottom: 16,
  },
  inputGroup: {
    marginBottom: 16,
  },
  label: {
    fontSize: 14,
    color: '#3d4a3e',
    marginBottom: 8,
    fontWeight: '600',
  },
  input: {
    borderWidth: 1,
    borderColor: '#bbcbbb',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    backgroundColor: '#ffffff',
    color: '#181c1c',
  },
  timeSlotContainer: {
    flexDirection: 'row',
    gap: 10,
  },
  timeSlot: {
    borderWidth: 1,
    borderColor: '#bbcbbb',
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 16,
    backgroundColor: '#f1f4f3',
  },
  timeSlotActive: {
    borderWidth: 2,
    borderColor: '#006d37',
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 16,
    backgroundColor: '#006d37',
  },
  timeSlotText: {
    color: '#5d5e61',
    fontWeight: 'bold',
  },
  timeSlotTextActive: {
    color: '#ffffff',
    fontWeight: 'bold',
  },
  button: {
    backgroundColor: '#fe6b00', // Action Orange
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
    elevation: 3,
  },
  buttonText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});