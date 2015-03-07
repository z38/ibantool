package z38.ibantool;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import ch.sic.ibantool.Main;
import ch.sic.ibantool.RecordIban;

public class Bridge {
	public static final int VFLAG_INVALID_INPUT = 29;

	public static void main(String[] args) throws IOException {
		BufferedReader reader = new BufferedReader(new InputStreamReader(System.in));
		Main converter = new ch.sic.ibantool.Main();
		String line;
		while ((line = reader.readLine()) != null) {
			String[] parts = line.split(";");
			if (parts.length != 2) {
				System.out.println(String.format("%d;", VFLAG_INVALID_INPUT));
				continue;
			}

			RecordIban input = new ch.sic.ibantool.RecordIban();
			input.BCPC = new StringBuffer(parts[0]);
			input.KoZe = new StringBuffer(parts[1]);
			RecordIban output = converter.IBANConvert(input);
			System.out.println(output.VFlag.toString() + ";" + output.Iban.toString());
		}
	}
}
