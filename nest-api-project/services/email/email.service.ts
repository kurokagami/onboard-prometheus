import { Injectable } from '@nestjs/common';
import * as nodemailer from 'nodemailer';

@Injectable()
export class EmailService {
  private transporter;

  constructor() {
    // Configuração do Nodemailer com conta do Gmail
    this.transporter = nodemailer.createTransport({
      service: 'gmail',
      auth: {
        user: 'sparking.exemple@gmail.com', // email de configuração
        pass: 'cjxa nkhe efjj pzpo', // token de aplicativo
      },
      secure: true,
    });
  }

  async sendEmail(mailOptions: any) {
    try {
      const info = await this.transporter.sendMail({
        from: mailOptions.from, 
        to: mailOptions.to,     
        subject: mailOptions.subject, 
        text: mailOptions.text,
        html: mailOptions.html, 
      });
      console.log('Email enviado: ' + info.response);
    } catch (error) {
      console.error('Erro ao enviar email:', error);
      throw error; // Repassa o erro para tratamento na controller
    }
  }
}
