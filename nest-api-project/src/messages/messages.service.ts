import { Injectable } from '@nestjs/common';
import { EmailService } from 'services/email/email.service';
import { CreateMessageDto } from './messages.dto';

@Injectable()
export class MessagesService {
  constructor(private readonly emailService: EmailService) {}

  async processAndSendEmail(messageData: CreateMessageDto) {
    const mailOptions = {
        from: `${messageData.name} <${messageData.email}>`, 
        to: 'sparking.exemple@gmail.com', // Destinatário fixo
        subject: `Nova mensagem de ${messageData.name} <${messageData.email}>`, // Assunto
        text: `Mensagem de: ${messageData.name}\nTelefone: (${messageData.ddd}) ${messageData.phone}\n\n${messageData.message}`, // Corpo em texto
        html: `<p>Nome: <strong>${messageData.name}</strong></p> 
               <p>Email: <strong>${messageData.email}</strong></p>
               <p>Telefone: <strong>(${messageData.ddd}) ${messageData.phone}</strong></p>
               <p>${messageData.message}</p>` // Corpo em HTML
      };

    await this.emailService.sendEmail(mailOptions);
  }
}
